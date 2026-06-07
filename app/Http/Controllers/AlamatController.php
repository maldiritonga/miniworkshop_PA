<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        // Maksimal 3 alamat
        if ($user->alamats()->count() >= 3) {
            \flash('Maksimal 3 alamat yang dapat disimpan.')->error();
            return redirect()->route('profile.edit');
        }

        $request->validate([
            'label'          => 'required|string|max:50',
            'nama_penerima'  => 'required|string|max:100',
            'no_hp'          => 'required|string|max:20',
            'alamat_lengkap' => 'required|string|max:500',
        ], [
            'label.required'          => 'Label alamat wajib diisi.',
            'nama_penerima.required'  => 'Nama penerima wajib diisi.',
            'no_hp.required'          => 'Nomor HP wajib diisi.',
            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi.',
        ]);

        $isFirst = $user->alamats()->count() === 0;

        Alamat::create([
            'id_user'        => $user->id_user,
            'label'          => $request->label,
            'nama_penerima'  => $request->nama_penerima,
            'no_hp'          => $request->no_hp,
            'alamat_lengkap' => $request->alamat_lengkap,
            'is_utama'       => $isFirst, // alamat pertama otomatis jadi utama
        ]);

        \flash('Alamat berhasil ditambahkan.')->success();
        return redirect()->route('profile.edit');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $alamat = Alamat::where('id_user', $user->id_user)->findOrFail($id);

        $request->validate([
            'label'          => 'required|string|max:50',
            'nama_penerima'  => 'required|string|max:100',
            'no_hp'          => 'required|string|max:20',
            'alamat_lengkap' => 'required|string|max:500',
        ]);

        $alamat->update([
            'label'          => $request->label,
            'nama_penerima'  => $request->nama_penerima,
            'no_hp'          => $request->no_hp,
            'alamat_lengkap' => $request->alamat_lengkap,
        ]);

        \flash('Alamat berhasil diperbarui.')->success();
        return redirect()->route('profile.edit');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $alamat = Alamat::where('id_user', $user->id_user)->findOrFail($id);

        $wasUtama = $alamat->is_utama;
        $alamat->delete();

        // Jika yang dihapus adalah alamat utama, set alamat pertama yang tersisa jadi utama
        if ($wasUtama) {
            $sisaAlamat = $user->alamats()->first();
            if ($sisaAlamat) {
                $sisaAlamat->update(['is_utama' => true]);
            }
        }

        \flash('Alamat berhasil dihapus.')->success();
        return redirect()->route('profile.edit');
    }

    public function setUtama($id)
    {
        $user = Auth::user();
        $alamat = Alamat::where('id_user', $user->id_user)->findOrFail($id);

        // Reset semua alamat user jadi bukan utama
        $user->alamats()->update(['is_utama' => false]);

        // Set alamat ini jadi utama
        $alamat->update(['is_utama' => true]);

        \flash('Alamat utama berhasil diubah.')->success();
        return redirect()->route('profile.edit');
    }
}
