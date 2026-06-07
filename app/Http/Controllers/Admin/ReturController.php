<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Retur;
use App\Notifications\ReturDitolak;
use App\Notifications\ReturMenungguRekening;
use App\Notifications\ReturUangDitransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReturController extends Controller
{
    public function index()
    {
        $retur = Retur::with(['pesanan.user', 'produk'])
            ->latest()
            ->paginate(10);

        return view('admin.retur.index', compact('retur'));
    }

    public function show($id)
    {
        $retur = Retur::with(['pesanan.user', 'pesanan.pembayaran', 'produk'])
            ->findOrFail($id);

        return view('admin.retur.show', compact('retur'));
    }

    /**
     * Terima retur: status diajukan → menunggu_rekening
     */
    public function terima(Request $request, $id)
    {
        $retur = Retur::with(['pesanan.user'])->findOrFail($id);

        if ($retur->status_retur !== Retur::STATUS_DIAJUKAN) {
            \flash('Retur tidak dalam status yang dapat diterima.')->error();
            return redirect()->back();
        }

        $retur->update(['status_retur' => Retur::STATUS_MENUNGGU_REKENING]);
        $retur->pesanan->update(['status_pesanan' => 'diretur']);

        if ($retur->pesanan->user) {
            $retur->pesanan->user->notify(new ReturMenungguRekening($retur));
        }

        \flash('Retur diterima. Pelanggan akan diminta mengisi nomor rekening.')->success();
        return redirect()->back();
    }

    /**
     * Tolak retur: status diajukan → ditolak
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        $retur = Retur::with(['pesanan.user'])->findOrFail($id);

        if ($retur->status_retur !== Retur::STATUS_DIAJUKAN) {
            \flash('Retur tidak dalam status yang dapat ditolak.')->error();
            return redirect()->back();
        }

        $retur->update([
            'status_retur'     => Retur::STATUS_DITOLAK,
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);
        $retur->pesanan->update(['status_pesanan' => 'selesai']);

        if ($retur->pesanan->user) {
            $retur->pesanan->user->notify(new ReturDitolak($retur));
        }

        \flash('Retur berhasil ditolak.')->success();
        return redirect()->back();
    }

    /**
     * Konfirmasi barang sampai: menunggu_barang → menunggu_transfer
     */
    public function konfirmasiBarang(Request $request, $id)
    {
        $retur = Retur::with(['produk'])->findOrFail($id);

        if ($retur->status_retur !== Retur::STATUS_MENUNGGU_BARANG) {
            \flash('Retur tidak dalam status menunggu barang.')->error();
            return redirect()->back();
        }

        DB::transaction(function () use ($retur) {
            // Kembalikan stok produk yang diretur
            if ($retur->produk) {
                $retur->produk->increment('stok', 1);
                // Sinkronkan status produk (aktif/nonaktif) sesuai stok terbaru
                $retur->produk->fresh()->update([
                    'status_produk' => $retur->produk->fresh()->stok > 0 ? 'aktif' : 'nonaktif',
                ]);
            }

            $retur->update(['status_retur' => Retur::STATUS_MENUNGGU_TRANSFER]);
        });

        \flash('Barang retur dikonfirmasi diterima. Stok produk telah dikembalikan. Silakan proses transfer dana.')->success();
        return redirect()->back();
    }

    /**
     * Upload bukti transfer: menunggu_transfer → uang_ditransfer
     */
    public function uploadBuktiTransfer(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $retur = Retur::with(['pesanan.user'])->findOrFail($id);

        if ($retur->status_retur !== Retur::STATUS_MENUNGGU_TRANSFER) {
            \flash('Retur tidak dalam status menunggu transfer.')->error();
            return redirect()->back();
        }

        $file = $request->file('bukti_transfer');
        $nama = 'bukti_transfer_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images/bukti_transfer'), $nama);

        $retur->update([
            'status_retur'   => Retur::STATUS_UANG_DITRANSFER,
            'bukti_transfer' => $nama,
        ]);

        if ($retur->pesanan->user) {
            $retur->pesanan->user->notify(new ReturUangDitransfer($retur));
        }

        \flash('Bukti transfer berhasil dikirim. Pelanggan akan mendapat notifikasi.')->success();
        return redirect()->back();
    }
}
