<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReturController extends Controller
{
    /**
     * Pelanggan ajukan retur baru
     */
    public function store(Request $request, $id_pesanan)
    {
        $user = Auth::user();

        $pesanan = Pesanan::where('id_user', $user->id_user)
            ->where('status_pesanan', 'selesai')
            ->findOrFail($id_pesanan);

        $request->validate([
            'id_produk'      => 'required|exists:produk,id_produk',
            'alasan_retur'   => 'required|string|max:500',
            'foto_bukti'     => 'required|array|min:1|max:3',
            'foto_bukti.*'   => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'id_produk.required'    => 'Produk tidak valid.',
            'alasan_retur.required' => 'Alasan retur wajib diisi.',
            'alasan_retur.max'      => 'Alasan maksimal 500 karakter.',
            'foto_bukti.required'   => 'Minimal 1 foto bukti wajib diunggah.',
            'foto_bukti.min'        => 'Minimal 1 foto bukti wajib diunggah.',
            'foto_bukti.max'        => 'Maksimal 3 foto bukti yang dapat diunggah.',
            'foto_bukti.*.image'    => 'File harus berupa gambar.',
            'foto_bukti.*.mimes'    => 'Format foto harus JPG, PNG, atau WebP.',
            'foto_bukti.*.max'      => 'Ukuran setiap foto maksimal 2MB.',
        ]);

        // Pastikan produk memang ada di pesanan ini
        $detail = DetailPesanan::where('id_pesanan', $id_pesanan)
            ->where('id_produk', $request->id_produk)
            ->firstOrFail();

        // Cek apakah produk ini sudah pernah diretur dari pesanan yang sama
        $sudahRetur = Retur::where('id_pesanan', $id_pesanan)
            ->where('id_produk', $request->id_produk)
            ->whereNotIn('status_retur', ['ditolak'])
            ->exists();

        if ($sudahRetur) {
            \flash('Produk ini sudah pernah diajukan retur.')->error();
            return redirect()->route('pesanan.show', $id_pesanan);
        }

        // Upload foto bukti (1-3 foto)
        $namaFoto = [];
        foreach ($request->file('foto_bukti') as $foto) {
            $nama = 'retur_' . time() . '_' . Str::random(8) . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('images/retur'), $nama);
            $namaFoto[] = $nama;
        }

        Retur::create([
            'id_pesanan'   => $id_pesanan,
            'id_produk'    => $request->id_produk,
            'alasan_retur' => $request->alasan_retur,
            'foto_bukti'   => $namaFoto,
            'status_retur' => Retur::STATUS_DIAJUKAN,
        ]);

        \flash('Pengajuan retur berhasil dikirim. Admin akan segera memprosesnya.')->success();
        return redirect()->route('pesanan.show', $id_pesanan);
    }

    /**
     * Pelanggan submit nomor rekening (menunggu_rekening → menunggu_barang)
     */
    public function kirimRekening(Request $request, $id_pesanan, $id_retur)
    {
        $user = Auth::user();

        $retur = Retur::whereHas('pesanan', fn($q) => $q->where('id_user', $user->id_user))
            ->where('id_pesanan', $id_pesanan)
            ->findOrFail($id_retur);

        if ($retur->status_retur !== Retur::STATUS_MENUNGGU_REKENING) {
            \flash('Status retur tidak valid untuk aksi ini.')->error();
            return redirect()->route('pesanan.show', $id_pesanan);
        }

        $request->validate([
            'nama_bank'              => 'required|string|max:100',
            'no_rekening'            => 'required|string|max:50',
            'nama_pemilik_rekening'  => 'required|string|max:150',
        ], [
            'nama_bank.required'             => 'Nama bank wajib diisi.',
            'no_rekening.required'           => 'Nomor rekening wajib diisi.',
            'nama_pemilik_rekening.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        $retur->update([
            'nama_bank'             => $request->nama_bank,
            'no_rekening'           => $request->no_rekening,
            'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
            'status_retur'          => Retur::STATUS_MENUNGGU_BARANG,
        ]);

        \flash('Data rekening berhasil disimpan. Silakan kirim barang ke toko.')->success();
        return redirect()->route('pesanan.show', $id_pesanan);
    }

    /**
     * Pelanggan konfirmasi uang sudah masuk (uang_ditransfer → selesai)
     */
    public function konfirmasiSelesai(Request $request, $id_pesanan, $id_retur)
    {
        $user = Auth::user();

        $retur = Retur::whereHas('pesanan', fn($q) => $q->where('id_user', $user->id_user))
            ->where('id_pesanan', $id_pesanan)
            ->findOrFail($id_retur);

        if ($retur->status_retur !== Retur::STATUS_UANG_DITRANSFER) {
            \flash('Status retur tidak valid untuk aksi ini.')->error();
            return redirect()->route('pesanan.show', $id_pesanan);
        }

        $retur->update(['status_retur' => Retur::STATUS_SELESAI]);
        $retur->pesanan->update(['status_pesanan' => 'selesai']);

        \flash('Retur selesai. Terima kasih telah mengkonfirmasi penerimaan dana.')->success();
        return redirect()->route('pesanan.show', $id_pesanan);
    }
}
