<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $alamats = $user->alamats()->orderByDesc('is_utama')->orderBy('created_at')->get();

        return view('pelanggan.profile.edit', compact('user', 'alamats'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->nama     = $request->nama;
        $user->email    = $request->email;
        $user->no_hp    = $request->no_hp;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        \flash('Informasi akun berhasil diperbarui.')->success();
        return Redirect::route('profile.edit');
    }

    public function uploadFoto(Request $request): RedirectResponse
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto_profil.required' => 'Pilih foto terlebih dahulu.',
            'foto_profil.image'    => 'File harus berupa gambar.',
            'foto_profil.mimes'    => 'Format foto harus JPG, PNG, atau WebP.',
            'foto_profil.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->foto_profil) {
            $oldPath = public_path('images/profil/' . $user->foto_profil);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Simpan foto baru
        $file = $request->file('foto_profil');
        $nama = 'profil_' . $user->id_user . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images/profil'), $nama);

        $user->foto_profil = $nama;
        $user->save();

        \flash('Foto profil berhasil diperbarui.')->success();
        return Redirect::route('profile.edit');
    }

    public function deleteFoto(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->foto_profil) {
            $oldPath = public_path('images/profil/' . $user->foto_profil);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

            $user->foto_profil = null;
            $user->save();
        }

        \flash('Foto profil berhasil dihapus.')->success();
        return Redirect::route('profile.edit');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
