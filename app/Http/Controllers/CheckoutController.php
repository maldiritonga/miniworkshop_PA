<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Keranjang;
use App\Models\KeranjangDetail;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    protected function simpanAlamatCheckout($user, string $noHp, string $alamatPengiriman): Alamat
    {
        $alamatSudahAda = $user->alamats()
            ->where('alamat_lengkap', $alamatPengiriman)
            ->where('no_hp', $noHp)
            ->first();

        if (!$alamatSudahAda && $user->alamats()->count() >= 3) {
            throw ValidationException::withMessages([
                'alamat_pengiriman' => 'Maksimal 3 alamat yang dapat disimpan.',
            ]);
        }

        $user->alamats()->update(['is_utama' => false]);

        if ($alamatSudahAda) {
            $alamatSudahAda->update([
                'label' => $alamatSudahAda->label ?: 'Alamat Utama',
                'nama_penerima' => $user->nama,
                'no_hp' => $noHp,
                'alamat_lengkap' => $alamatPengiriman,
                'is_utama' => true,
            ]);

            return $alamatSudahAda->fresh();
        }

        return Alamat::create([
            'id_user' => $user->id_user,
            'label' => 'Alamat Utama',
            'nama_penerima' => $user->nama,
            'no_hp' => $noHp,
            'alamat_lengkap' => $alamatPengiriman,
            'is_utama' => true,
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $items = [];
        $subtotal = 0;
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
                    'harga' => $produk->harga_akhir,
                    'qty' => 1,
                    'id_produk' => $produk->id_produk
                ]
            ];
            $kategori = strtolower($produk->kategori->nama_kategori ?? '');
            $itemWeight = str_contains($kategori, 'sepatu') ? 1500 : 1000;
            $subtotal = $produk->harga_akhir;
            $totalWeight = $itemWeight; // qty * weight
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

            $subtotal = $items->sum(function($item) {
                return $item->produk->harga_akhir * $item->qty;
            });
            
            $totalWeight = $items->sum(function($item) {
                $kategori = strtolower($item->produk->kategori->nama_kategori ?? '');
                $itemWeight = str_contains($kategori, 'sepatu') ? 1500 : 1000;
                return $item->qty * $itemWeight;
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
            
            $subtotal = $items->sum(function($item) {
                return $item->produk->harga_akhir * $item->qty;
            });

            $totalWeight = $items->sum(function($item) {
                $kategori = strtolower($item->produk->kategori->nama_kategori ?? '');
                $itemWeight = str_contains($kategori, 'sepatu') ? 1500 : 1000;
                return $item->qty * $itemWeight;
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

        return view('pelanggan.checkout.index', compact('items', 'subtotal', 'totalWeight', 'alamat', 'noHp', 'isDirect', 'lastOrder', 'alamats', 'alamatUtama'));
    }

    public function saveAddress(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string|max:20',
            'alamat_pengiriman' => 'required|string|max:500',
        ], [
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'alamat_pengiriman.required' => 'Alamat lengkap wajib diisi.',
        ]);

        $alamat = $this->simpanAlamatCheckout(
            Auth::user(),
            $request->no_hp,
            $request->alamat_pengiriman
        );

        return response()->json([
            'message' => 'Alamat berhasil disimpan dan dijadikan alamat utama.',
            'address' => $alamat,
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
            'alamat_pengiriman' => 'required|string',
            'metode_pembayaran' => 'required|string',
            'bank_tujuan' => 'nullable|string|in:BRI,BNI,BCA',
            'catatan' => 'nullable|string|max:500',
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
                    'harga' => $produk->harga_akhir,
                    'qty' => 1,
                    'produk' => $produk
                ]
            ];
            $total = $produk->harga_akhir + $ongkir;
        } elseif ($request->has('cart_item_ids')) {
            // Process partial cart
            $items = KeranjangDetail::whereIn('id_keranjang_detail', $request->cart_item_ids)->get();
            $total = $items->sum(function($item) {
                return $item->produk->harga_akhir * $item->qty;
            }) + $ongkir;
        } else {
            // Process full cart
            $keranjang = Keranjang::where('id_user', $user->id_user)->first();
            if (!$keranjang || $keranjang->detail()->count() == 0) {
                return redirect()->route('home')->with('error', 'Keranjang kosong.');
            }
            $items = KeranjangDetail::where('id_keranjang', $keranjang->id_keranjang)->get();
            $total = $items->sum(function($item) {
                return $item->produk->harga_akhir * $item->qty;
            }) + $ongkir;
        }

        DB::beginTransaction();
        try {
            if (!$user->alamats()->exists()) {
                $this->simpanAlamatCheckout(
                    $user,
                    $request->no_hp,
                    $request->alamat_pengiriman
                );
            }

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
                'catatan' => $request->catatan,
                'kurir' => $request->kurir,
            ]);

            // 2. Buat Detail Pesanan & Update Stok
            foreach ($items as $item) {
                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $item->id_produk,
                    'harga' => $item->produk ? $item->produk->harga_akhir : $item->harga,
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
