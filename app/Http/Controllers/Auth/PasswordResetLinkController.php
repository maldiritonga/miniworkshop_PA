<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    private const EXPIRES_MINUTES = 10;

    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.reset-password.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $request->session()->forget(['password_reset_email', 'password_reset_verified_at']);

        $userExists = User::where('email', $request->email)->exists();

        if ($userExists) {
            $code = (string) random_int(100000, 999999);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                ['token' => Hash::make($code), 'created_at' => now()]
            );

            try {
                Mail::to($request->email)->send(new PasswordResetCodeMail($code, self::EXPIRES_MINUTES));
            } catch (Throwable $e) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                report($e);

                return back()->withErrors([
                    'email' => 'Gagal mengirim email. Pastikan konfigurasi email (SMTP) sudah benar, lalu coba lagi.',
                ])->withInput($request->only('email'));
            }
        }

        return redirect()
            ->route('password.code.verify', ['email' => $request->email])
            ->with('status', 'Jika email terdaftar, Anda akan menerima kode reset password.');
    }
}
