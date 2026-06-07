<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use App\Services\PesananService;
use App\Services\BiteshipService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private PesananService $pesananService,
        private BiteshipService $biteshipService
    ) {}

    public function index()
    {
        $this->pesananService->cancelAllExpired();

        $pesanan = Pesanan::with(['user', 'pembayaran'])->latest()->paginate(10);
        return view('admin.pesanan.index', compact('pesanan'));
    }

    public function show(string $id)
    {
        $pesanan = Pesanan::with(['user', 'detail.produk', 'pembayaran'])->findOrFail($id);
        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function update(Request $request, string $id)
    {
        $pesanan = Pesanan::with(['detail.produk', 'pembayaran'])->findOrFail($id);

        $request->validate([
            'status_pesanan' => 'required|string',
            'kurir'          => 'nullable|string',
            'resi'           => 'nullable|string',
        ]);

        $statusBaru = $request->status_pesanan;
        $statusLama = $pesanan->status_pesanan;
        $isOffline  = $pesanan->tipe_pesanan === 'offline';

        // ── Pesanan OFFLINE: hanya boleh selesai ↔ dibatalkan ──
        if ($isOffline) {
            if (!in_array($statusBaru, ['selesai', 'dibatalkan'])) {
                \flash('Pesanan offline hanya bisa diubah ke Selesai atau Dibatalkan.')->error();
                return redirect()->back();
            }
            // Dibatalkan → kembalikan stok
            if ($statusBaru === 'dibatalkan' && $statusLama !== 'dibatalkan') {
                foreach ($pesanan->detail as $item) {
                    if ($item->produk) $item->produk->increment('stok', $item->qty);
                }
            }
            // Selesai lagi dari dibatalkan → kurangi stok
            if ($statusBaru === 'selesai' && $statusLama === 'dibatalkan') {
                foreach ($pesanan->detail as $item) {
                    if ($item->produk) $item->produk->decrement('stok', $item->qty);
                }
            }
            $pesanan->update(['status_pesanan' => $statusBaru]);
            \flash('Status pesanan berhasil diperbarui.')->success();
            return redirect()->back();
        }

        // ── Pesanan ONLINE: selesai, dibatalkan, diretur tidak bisa diubah ──
        if (in_array($statusLama, ['selesai', 'dibatalkan', 'diretur'])) {
            \flash('Status pesanan yang sudah selesai, dibatalkan, atau diretur tidak dapat diubah.')->error();
            return redirect()->back();
        }

        // Tidak boleh kembali ke menunggu_pembayaran jika sudah dibayar
        if ($statusBaru === 'menunggu_pembayaran' && $pesanan->pembayaran && $pesanan->pembayaran->status_pembayaran === 'berhasil') {
            \flash('Pesanan yang pembayarannya sudah dikonfirmasi tidak bisa dikembalikan ke menunggu pembayaran.')->error();
            return redirect()->back();
        }

        // Dibatalkan dari menunggu_pembayaran → kembalikan stok
        if ($statusBaru === 'dibatalkan' && $statusLama === 'menunggu_pembayaran') {
            $this->pesananService->batalkanDanKembalikanStok($pesanan);
            \flash('Pesanan dibatalkan dan stok produk dikembalikan ke katalog.')->success();
            return redirect()->back();
        }

        $pesanan->update([
            'status_pesanan' => $statusBaru,
            'kurir'          => $request->kurir,
            'resi'           => $request->resi,
        ]);

        \flash('Status pesanan berhasil diperbarui.')->success();
        return redirect()->back();
    }

    public function konfirmasiPembayaran(Request $request, string $id)
    {
        $pesanan = Pesanan::with('pembayaran')->findOrFail($id);

        $request->validate([
            'aksi'             => 'required|in:konfirmasi,tolak',
            'alasan_penolakan' => 'required_if:aksi,tolak|nullable|string|max:500',
        ], [
            'alasan_penolakan.required_if' => 'Alasan penolakan wajib diisi saat menolak bukti pembayaran.',
        ]);

        if ($request->aksi === 'konfirmasi') {
            // Update Pembayaran
            $pesanan->pembayaran()->update([
                'status_pembayaran'  => 'berhasil',
                'tanggal_pembayaran' => now(),
                'alasan_penolakan'   => null,
            ]);
            // Update Pesanan
            $pesanan->update(['status_pesanan' => 'dikemas']);
            
            \flash('Pembayaran berhasil dikonfirmasi. Status pesanan: Dikemas.')->success();
        } else {
            // Update Pembayaran
            $pesanan->pembayaran()->update([
                'status_pembayaran'  => 'ditolak',
                'bukti_pembayaran'   => null, // Pastikan file bukti dihapus dari record
                'alasan_penolakan'   => $request->alasan_penolakan,
            ]);
            // Update Pesanan kembali ke menunggu_pembayaran
            $pesanan->update(['status_pesanan' => 'menunggu_pembayaran']);
            
            \flash('Bukti pembayaran telah ditolak.')->warning();
        }

        return redirect()->back();
    }

    public function invoice(string $id)
    {
        $pesanan = Pesanan::with(['user', 'detail.produk', 'pembayaran'])->findOrFail($id);
        return view('admin.pesanan.invoice', compact('pesanan'));
    }

    public function labelPengiriman(string $id)
    {
        $pesanan = Pesanan::with(['user', 'detail.produk', 'pembayaran'])->findOrFail($id);
        return view('admin.pesanan.label-pengiriman', compact('pesanan'));
    }

    public function requestPickup(string $id)
    {
        $pesanan = Pesanan::with(['user', 'detail.produk'])->findOrFail($id);

        if (trim(strtolower($pesanan->tipe_pesanan ?? 'online')) === 'offline') {
            \flash('Pesanan offline tidak bisa menggunakan fitur request pickup.')->error();
            return redirect()->back();
        }

        if ($pesanan->biteship_order_id) {
            \flash('Pesanan ini sudah pernah di-request pickup (Order ID: '.$pesanan->biteship_order_id.').')->warning();
            return redirect()->back();
        }

        $response = $this->biteshipService->createOrder($pesanan);

        if ($response['success']) {
            $data = $response['data'];
            
            // Simpan resi & order_id dari biteship
            $pesanan->update([
                'biteship_order_id' => $data['id'] ?? null,
                'resi' => $data['courier']['waybill_id'] ?? null,
                'status_pesanan' => 'dikirim', // otomatis ubah status jadi dikirim
            ]);

            \flash('Berhasil request pickup! Kurir akan segera datang. No Resi: ' . ($data['courier']['waybill_id'] ?? '-'))->success();
        } else {
            \flash('Gagal request pickup: ' . $response['error'])->error();
        }

        return redirect()->back();
    }

    public function destroy(string $id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->delete();

        \flash('Pesanan berhasil dihapus.')->info();
        return redirect()->route('admin.pesanan.index');
    }
}
