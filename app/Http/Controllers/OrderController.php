<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Retur;
use App\Services\PesananService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(private PesananService $pesananService) {}

    public function index()
    {
        $this->pesananService->cancelAllExpired();

        $user = Auth::user();
        $pesanan = Pesanan::with(['detail.produk', 'pembayaran'])
            ->where('id_user', $user->id_user)
            ->latest()
            ->get();

        return view('pelanggan.pesanan.index', compact('pesanan'));
    }

    public function show($id, \App\Services\BiteshipService $biteship)
    {
        $user = Auth::user();
        $pesanan = Pesanan::with(['detail.produk', 'pembayaran', 'retur.produk'])
            ->where('id_user', $user->id_user)
            ->findOrFail($id);

        $this->pesananService->cancelIfExpired($pesanan);
        $pesanan->refresh();

        $tracking = null;
        if ($pesanan->resi && $pesanan->kurir) {
            $tracking = $biteship->getTracking($pesanan->resi, $pesanan->kurir);
        }

        return view('pelanggan.pesanan.show', compact('pesanan', 'tracking'));
    }

    public function invoice($id)
    {
        $user = Auth::user();
        $pesanan = Pesanan::with(['detail.produk', 'pembayaran', 'user'])
            ->where('id_user', $user->id_user)
            ->whereHas('pembayaran', fn ($q) => $q->where('status_pembayaran', 'berhasil'))
            ->findOrFail($id);

        return view('pelanggan.pesanan.invoice', compact('pesanan'));
    }

    public function labelRetur($id, $id_retur)
    {
        $user = Auth::user();
        $pesanan = Pesanan::with(['detail.produk', 'pembayaran', 'user', 'retur.produk'])
            ->where('id_user', $user->id_user)
            ->findOrFail($id);

        $retur = $pesanan->retur
            ->where('id_retur', (int) $id_retur)
            ->first();

        abort_if(! $retur || ! $retur->canPrintLabel(), 403, 'Label pengiriman retur tidak tersedia untuk produk ini.');

        $detailProduk = $pesanan->detail->firstWhere('id_produk', $retur->id_produk);

        return view('pelanggan.pesanan.label-retur', compact('pesanan', 'retur', 'detailProduk'));
    }

    public function batalkan($id)
    {
        $user = Auth::user();
        $pesanan = Pesanan::with(['detail.produk', 'pembayaran'])
            ->where('id_user', $user->id_user)
            ->where('status_pesanan', 'menunggu_pembayaran')
            ->findOrFail($id);

        if ($pesanan->pembayaran?->status_pembayaran === 'menunggu_konfirmasi') {
            \flash('Pesanan tidak dapat dibatalkan karena bukti pembayaran sedang diverifikasi.')->error();
            return redirect()->route('pesanan.show', $id);
        }

        if (! $this->pesananService->batalkanDanKembalikanStok($pesanan)) {
            \flash('Pesanan tidak dapat dibatalkan.')->error();
            return redirect()->route('pesanan.show', $id);
        }

        \flash('Pesanan #' . $id . ' berhasil dibatalkan. Stok produk telah dikembalikan ke katalog.')->success();
        return redirect()->route('pesanan.saya');
    }

    public function uploadBukti(Request $request, $id)
    {
        $user = Auth::user();
        $pesanan = Pesanan::with('pembayaran')
            ->where('id_user', $user->id_user)
            ->findOrFail($id);

        $this->pesananService->cancelIfExpired($pesanan);
        $pesanan->refresh();

        if ($pesanan->status_pesanan === 'dibatalkan') {
            \flash('Pesanan dibatalkan karena melewati batas waktu pembayaran 24 jam. Stok produk telah dikembalikan.')->error();
            return redirect()->route('pesanan.show', $id);
        }

        if (! $pesanan->canUploadBuktiPembayaran()) {
            \flash('Batas waktu unggah bukti pembayaran (24 jam) telah habis atau pesanan tidak dapat diubah.')->error();
            return redirect()->route('pesanan.show', $id);
        }

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'bukti_pembayaran.required' => 'Silakan pilih file bukti pembayaran.',
            'bukti_pembayaran.image'    => 'File harus berupa gambar.',
            'bukti_pembayaran.mimes'    => 'Format file harus JPG, JPEG, atau PNG.',
            'bukti_pembayaran.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $file = $request->file('bukti_pembayaran');
        $filename = 'bukti_' . $pesanan->id_pesanan . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images/bukti'), $filename);

        $pesanan->pembayaran->update([
            'bukti_pembayaran'  => $filename,
            'status_pembayaran' => 'menunggu_konfirmasi',
        ]);

        \flash('Bukti pembayaran berhasil diunggah. Menunggu konfirmasi admin.')->success();
        return redirect()->route('pesanan.show', $id);
    }

    public function konfirmasiDiterima($id)
    {
        $user = Auth::user();
        $pesanan = Pesanan::where('id_user', $user->id_user)
            ->where('status_pesanan', 'diantar')
            ->findOrFail($id);

        $pesanan->update(['status_pesanan' => 'selesai']);

        \flash('Terima kasih! Pesanan #' . $id . ' telah dikonfirmasi selesai.')->success();
        return redirect()->route('pesanan.show', $id);
    }
}
