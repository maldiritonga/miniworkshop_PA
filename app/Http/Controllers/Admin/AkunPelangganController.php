<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AkunPelangganController extends Controller
{
    public function index()
    {
        $pelanggan = User::where('role', 'pelanggan')->latest()->paginate(10);
        return view('admin.akun-pelanggan.index', compact('pelanggan'));
    }

    public function toggleBlock($id)
    {
        $pelanggan = User::where('role', 'pelanggan')->findOrFail($id);
        $pelanggan->is_blocked = !$pelanggan->is_blocked;
        $pelanggan->save();

        $status = $pelanggan->is_blocked ? 'diblokir' : 'diaktifkan kembali';
        \flash("Akun {$pelanggan->nama} berhasil {$status}.")->success();

        return redirect()->route('admin.akun-pelanggan.index');
    }
}
