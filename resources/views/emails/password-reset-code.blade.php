<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Reset Password</title>
</head>
<body style="margin:0;padding:0;background:#f9fafb;font-family:Arial,Helvetica,sans-serif;">
    <div style="max-width:560px;margin:0 auto;padding:28px 16px;">
        <div style="background:#ffffff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
            <h1 style="margin:0 0 8px 0;font-size:20px;line-height:28px;color:#111827;">Kode Reset Password</h1>
            <p style="margin:0 0 18px 0;font-size:14px;line-height:22px;color:#6b7280;">
                Gunakan kode berikut untuk reset password akun Mini Workshop. Kode ini berlaku {{ $expiresMinutes }} menit.
            </p>

            <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:14px;padding:18px;text-align:center;">
                <div style="font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#9a3412;font-weight:700;margin-bottom:8px;">
                    Kode Verifikasi
                </div>
                <div style="font-size:34px;letter-spacing:6px;color:#111827;font-weight:800;">
                    {{ $code }}
                </div>
            </div>

            <p style="margin:18px 0 0 0;font-size:12px;line-height:18px;color:#9ca3af;">
                Jika Anda tidak merasa meminta reset password, abaikan email ini.
            </p>
        </div>
        <p style="margin:12px 0 0 0;font-size:11px;line-height:18px;color:#9ca3af;text-align:center;">
            &copy; {{ date('Y') }} Mini Workshop
        </p>
    </div>
</body>
</html>

