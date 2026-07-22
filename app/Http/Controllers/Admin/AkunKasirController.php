<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AkunKasirController extends Controller
{
    public function index()
    {
        $kasir = User::whereIn('role', ['kasir', 'admin'])->where('id_user', '!=', auth()->id())->latest()->paginate(10);
        return view('admin.akun-kasir.index', compact('kasir'));
    }

    public function create()
    {
        return view('admin.akun-kasir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'no_hp' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:kasir,admin'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.akun-kasir.index')->with('success', 'Akun staff berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kasir = User::whereIn('role', ['kasir', 'admin'])->findOrFail($id);
        return view('admin.akun-kasir.edit', compact('kasir'));
    }

    public function update(Request $request, $id)
    {
        $kasir = User::whereIn('role', ['kasir', 'admin'])->findOrFail($id);

        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$id.',id_user'],
            'no_hp' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:kasir,admin'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $kasir->nama = $request->nama;
        $kasir->email = $request->email;
        $kasir->no_hp = $request->no_hp;
        $kasir->role = $request->role;

        if ($request->filled('password')) {
            $kasir->password = Hash::make($request->password);
        }

        $kasir->save();

        return redirect()->route('admin.akun-kasir.index')->with('success', 'Akun staff berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kasir = User::whereIn('role', ['kasir', 'admin'])->findOrFail($id);
        $kasir->delete();

        return redirect()->route('admin.akun-kasir.index')->with('success', 'Akun staff berhasil dihapus.');
    }
}
