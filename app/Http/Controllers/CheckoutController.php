<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\KeranjangDetail;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $items = [];
        $total = 0;
        $totalWeight = 0;
        $isDirect = false;

        if ($request->has('produk_id')) {
            // Direct purchase (Beli Sekarang)
            $produk = Produk::findOrFail($request->produk_id);
            if ($produk->stok <= 0) {
                 \flash("Stok produk {$produk->nama_produk} sudah habis.")->error();
                 return redirect()->back();
            }
            $items = [
                (object)[
                    'produk' => $produk,
                    'harga' => $produk->harga,
                    'qty' => 1,
                    'id_produk' => $produk->id_produk
                ]
            ];
            $total = $produk->harga + 15000;
            $totalWeight = 500; // 1 qty * 500g
            $isDirect = true;
        } elseif ($request->has('cart_item_ids')) {
            // Partial cart checkout
            $items = KeranjangDetail::with('produk')
                ->whereIn('id_keranjang_detail', $request->cart_item_ids)
                ->get();
            
            if ($items->isEmpty()) {
                \flash('Pilih produk yang ingin dibeli.')->warning();
                return redirect()->route('keranjang.index');
            }

            foreach ($items as $item) {
                 if (!$item->produk || $item->produk->stok <= 0) {
                      \flash("Produk " . ($item->produk->nama_produk ?? '') . " sudah habis.")->error();
                      return redirect()->route('keranjang.index');
                 }
            }

            $total = $items->sum(function($item) {
                return $item->harga * $item->qty;
            }) + 15000;
            
            $totalWeight = $items->sum(function($item) {
                return $item->qty * 500;
            });
        } else {
            // Full cart checkout (default)
            $keranjang = Keranjang::where('id_user', $user->id_user)->first();
            
            if (!$keranjang || $keranjang->detail()->count() == 0) {
                \flash('Keranjang Anda kosong.')->warning();
                return redirect()->route('keranjang.index');
            }

            $items = KeranjangDetail::with('produk')->where('id_keranjang', $keranjang->id_keranjang)->get();
            
            foreach ($items as $item) {
                 if (!$item->produk || $item->produk->stok <= 0) {
                      \flash("Terdapat produk yang sudah habis di keranjang Anda. Harap hapus atau batalkan centang produk tersebut.")->error();
                      return redirect()->route('keranjang.index');
                 }
            }
            
            $total = $items->sum(function($item) {
                return $item->harga * $item->qty;
            }) + 15000;

            $totalWeight = $items->sum(function($item) {
                return $item->qty * 500;
            });
        }

        // Ambil alamat utama dari profil, fallback ke pesanan terakhir
        $alamatUtama = $user->alamatUtama()->first();
        $lastOrder = null;

        if ($alamatUtama) {
            $alamat = $alamatUtama->alamat_lengkap;
            $noHp   = $alamatUtama->no_hp;
        } else {
            $lastOrder = Pesanan::where('id_user', $user->id_user)
                ->whereNotNull('alamat_pengiriman')
                ->latest('created_at')
                ->first();
            $alamat = $lastOrder ? $lastOrder->alamat_pengiriman : '';
            $noHp   = $lastOrder ? $lastOrder->no_hp : ($user->no_hp ?? '');
        }

        $alamats = $user->alamats()->orderByDesc('is_utama')->orderBy('created_at')->get();

        return view('pelanggan.checkout.index', compact('items', 'total', 'totalWeight', 'alamat', 'noHp', 'isDirect', 'lastOrder', 'alamats', 'alamatUtama'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
            'alamat_pengiriman' => 'required|string',
            'metode_pembayaran' => 'required|string',
            'bank_tujuan' => 'nullable|string|in:BRI,BNI,BCA',
            'ongkir' => 'required|numeric',
            'kurir' => 'required|string|in:jne,sicepat,jnt',
        ]);

        $user = Auth::user();
        $items = [];
        $total = 0;
        $ongkir = $request->ongkir;

        if ($request->has('produk_id')) {
            // Process direct purchase
            $produk = Produk::findOrFail($request->produk_id);
            $items = [
                (object)[
                    'id_produk' => $produk->id_produk,
                    'harga' => $produk->harga,
                    'qty' => 1,
                    'produk' => $produk
                ]
            ];
            $total = $produk->harga + $ongkir;
        } elseif ($request->has('cart_item_ids')) {
            // Process partial cart
            $items = KeranjangDetail::whereIn('id_keranjang_detail', $request->cart_item_ids)->get();
            $total = $items->sum(function($item) {
                return $item->harga * $item->qty;
            }) + $ongkir;
        } else {
            // Process full cart
            $keranjang = Keranjang::where('id_user', $user->id_user)->first();
            if (!$keranjang || $keranjang->detail()->count() == 0) {
                return redirect()->route('home')->with('error', 'Keranjang kosong.');
            }
            $items = KeranjangDetail::where('id_keranjang', $keranjang->id_keranjang)->get();
            $total = $items->sum(function($item) {
                return $item->harga * $item->qty;
            }) + $ongkir;
        }

        DB::beginTransaction();
        try {
            // Validasi stok sebelum proses
            foreach ($items as $item) {
                $produk = Produk::find($item->id_produk);
                if (!$produk || $produk->stok < $item->qty) {
                    $namaProduk = $produk->nama_produk ?? 'Produk';
                    \flash("Stok {$namaProduk} tidak mencukupi. Stok tersedia: " . ($produk->stok ?? 0))->error();
                    return redirect()->back();
                }
            }


            // 1. Buat Pesanan
            $pesanan = Pesanan::create([
                'id_user' => $user->id_user,
                'no_hp' => $request->no_hp,
                'tanggal_pesanan' => now(),
                'total_harga' => $total,
                'status_pesanan' => 'menunggu_pembayaran',
                'batas_pembayaran_at' => now()->addHours(Pesanan::BATAS_PEMBAYARAN_JAM),
                'tipe_pesanan' => 'online',
                'alamat_pengiriman' => $request->alamat_pengiriman,
                'kurir' => $request->kurir,
            ]);

            // 2. Buat Detail Pesanan & Update Stok
            foreach ($items as $item) {
                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $item->id_produk,
                    'harga' => $item->harga,
                    'qty' => $item->qty,
                ]);

                // Kurangi stok produk
                $produk = Produk::find($item->id_produk);
                if ($produk) {
                    $produk->stok -= $item->qty;
                    $produk->save();
                }
            }

            // 3. Buat Pembayaran
            Pembayaran::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'bank_tujuan' => $request->bank_tujuan ?? null,
                'status_pembayaran' => 'belum_dibayar',
                'tanggal_pembayaran' => null,
            ]);

            // 4. Kosongkan Keranjang untuk item yang dipesan
            if ($request->has('cart_item_ids')) {
                KeranjangDetail::whereIn('id_keranjang_detail', $request->cart_item_ids)->delete();
            } elseif (!$request->has('produk_id')) {
                $keranjang = Keranjang::where('id_user', $user->id_user)->first();
                if ($keranjang) {
                    KeranjangDetail::where('id_keranjang', $keranjang->id_keranjang)->delete();
                }
            }

            DB::commit();

            \flash('Pesanan berhasil dibuat! Lakukan pembayaran dan unggah bukti dalam ' . Pesanan::BATAS_PEMBAYARAN_JAM . ' jam.')->success();
            return redirect()->route('pesanan.saya');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function buyNow(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return redirect()->route('checkout.index', ['produk_id' => $id]);
    }
}
