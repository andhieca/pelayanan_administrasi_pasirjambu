<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                    
                    <!-- Header with gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 50%, #3b7ddd 100%); padding: 36px 40px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <img src="{{ config('app.url') }}/logo-kab-bandung.png" alt="Logo Kabupaten Bandung" width="72" height="72" style="display: block; margin-bottom: 16px; border-radius: 8px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">
                                            Pelayanan Administrasi
                                        </h1>
                                        <p style="margin: 6px 0 0; color: #bdd4ec; font-size: 13px; font-weight: 500; letter-spacing: 1px; text-transform: uppercase;">
                                            Kecamatan Pasirjambu — Kabupaten Bandung
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px;">
                            <!-- Greeting -->
                            <h2 style="margin: 0 0 8px; color: #1e293b; font-size: 22px; font-weight: 700;">
                                Halo, {{ $user->name ?? 'Pengguna' }}!
                            </h2>
                            <p style="margin: 0 0 28px; color: #64748b; font-size: 14px; line-height: 1.6;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda pada sistem Pelayanan Administrasi Kecamatan Pasirjambu.
                            </p>

                            <!-- Info Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 28px;">
                                <tr>
                                    <td style="background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 0 8px 8px 0; padding: 16px 20px;">
                                        <p style="margin: 0; color: #1e40af; font-size: 13px; line-height: 1.6;">
                                            <strong>📌 Informasi:</strong> Klik tombol di bawah ini untuk membuat kata sandi baru. Tautan ini hanya berlaku selama <strong>60 menit</strong>.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 700; padding: 16px 48px; border-radius: 10px; letter-spacing: 0.3px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);">
                                            🔐 Atur Ulang Kata Sandi
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">

                            <!-- Security Notice -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #fefce8; border-left: 4px solid #eab308; border-radius: 0 8px 8px 0; padding: 16px 20px;">
                                        <p style="margin: 0; color: #854d0e; font-size: 13px; line-height: 1.6;">
                                            <strong>⚠️ Peringatan Keamanan:</strong> Jika Anda tidak merasa meminta pengaturan ulang kata sandi, abaikan email ini. Akun Anda tetap aman.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Alternative Link -->
                            <p style="margin: 0 0 8px; color: #94a3b8; font-size: 12px;">
                                Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser Anda:
                            </p>
                            <p style="margin: 0; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; word-break: break-all;">
                                <a href="{{ $url }}" style="color: #2563eb; font-size: 12px; text-decoration: none;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 28px 40px; border-top: 1px solid #e2e8f0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 6px; color: #475569; font-size: 13px; font-weight: 600;">
                                            Pelayanan Administrasi Kecamatan Pasirjambu
                                        </p>
                                        <p style="margin: 0 0 4px; color: #94a3b8; font-size: 11px; line-height: 1.5;">
                                            Kabupaten Bandung, Jawa Barat
                                        </p>
                                        <p style="margin: 12px 0 0; color: #cbd5e1; font-size: 11px;">
                                            &copy; {{ date('Y') }} Kecamatan Pasirjambu. Hak cipta dilindungi.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

                <!-- Bottom Note -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center" style="padding: 20px 40px;">
                            <p style="margin: 0; color: #94a3b8; font-size: 11px; line-height: 1.5;">
                                Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
