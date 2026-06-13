<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Cek apakah akun diblokir
                if ($user->is_blocked) {
                    return redirect('/login')->withErrors(['email' => 'Akun Anda telah diblokir. Silakan hubungi admin untuk informasi lebih lanjut.']);
                }

                // Update google_id if it's missing (matched by email)
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                
                Auth::login($user);
                return redirect()->intended('/dashboard');
            } else {
                $newUser = User::create([
                    'nama' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'role' => 'pelanggan',
                    // Provide a random password since it's required by db schema
                    'password' => Hash::make(Str::random(24)),
                    'is_blocked' => false,
                ]);

                Auth::login($newUser);
                return redirect()->intended('/dashboard');
            }
        } catch (\Exception $e) {
            return redirect('/login')->with('status', 'Terjadi kesalahan saat login dengan Google: ' . $e->getMessage());
        }
    }
}
