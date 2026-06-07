<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PasswordResetCodeController extends Controller
{
    private const EXPIRES_MINUTES = 10;

    public function create(Request $request): View
    {
        return view('auth.reset-password.verify-reset-code', [
            'email' => $request->query('email'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !$record->created_at) {
            return back()->withErrors(['code' => 'Kode tidak valid atau sudah kedaluwarsa.'])->withInput($request->only('email'));
        }

        $expiredAt = now()->subMinutes(self::EXPIRES_MINUTES);

        if (\Carbon\Carbon::parse($record->created_at) < $expiredAt) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['code' => 'Kode sudah kedaluwarsa. Silakan minta kode baru.'])->withInput($request->only('email'));
        }

        if (!Hash::check($request->code, $record->token)) {
            return back()->withErrors(['code' => 'Kode tidak valid.'])->withInput($request->only('email'));
        }

        $request->session()->put('password_reset_email', $request->email);
        $request->session()->put('password_reset_verified_at', now()->timestamp);

        return redirect()->route('password.code.reset');
    }

    public function showReset(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');
        $verifiedAt = $request->session()->get('password_reset_verified_at');

        if (!$email || !$verifiedAt) {
            return redirect()->route('password.request');
        }

        $expiredAt = now()->subMinutes(self::EXPIRES_MINUTES)->timestamp;

        if ($verifiedAt < $expiredAt) {
            $request->session()->forget(['password_reset_email', 'password_reset_verified_at']);
            return redirect()->route('password.request')->with('status', 'Kode sudah kedaluwarsa. Silakan minta kode baru.');
        }

        return view('auth.reset-password.reset-password-code', [
            'email' => $email,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');
        $verifiedAt = $request->session()->get('password_reset_verified_at');

        if (!$email || !$verifiedAt) {
            return redirect()->route('password.request');
        }

        $expiredAt = now()->subMinutes(self::EXPIRES_MINUTES)->timestamp;

        if ($verifiedAt < $expiredAt) {
            $request->session()->forget(['password_reset_email', 'password_reset_verified_at']);
            return redirect()->route('password.request')->with('status', 'Kode sudah kedaluwarsa. Silakan minta kode baru.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            $request->session()->forget(['password_reset_email', 'password_reset_verified_at']);
            return redirect()->route('password.request')->with('status', 'Jika email terdaftar, Anda akan menerima kode reset password.');
        }

        $payload = [
            'password' => Hash::make($request->password),
        ];

        if (Schema::hasColumn('users', 'remember_token')) {
            $payload['remember_token'] = Str::random(60);
        }

        $user->forceFill($payload)->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        $request->session()->forget(['password_reset_email', 'password_reset_verified_at']);

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login kembali.');
    }
}
