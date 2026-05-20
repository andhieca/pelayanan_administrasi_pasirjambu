<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Tidak Valid - Kecamatan Pasirjambu</title>
    <meta name="description" content="Verifikasi dokumen gagal - dokumen tidak ditemukan dalam sistem">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 50%, rgba(239, 68, 68, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(239, 68, 68, 0.05) 0%, transparent 50%);
            animation: bgFloat 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes bgFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(1deg); }
            66% { transform: translate(-20px, 20px) rotate(-1deg); }
        }

        .card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            animation: cardSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        @keyframes cardSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .status-icon {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            position: relative;
            animation: iconPulse 2s ease-in-out infinite;
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.3),
                        0 0 60px rgba(239, 68, 68, 0.1);
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 0 30px rgba(239, 68, 68, 0.3), 0 0 60px rgba(239, 68, 68, 0.1); }
            50% { box-shadow: 0 0 40px rgba(239, 68, 68, 0.5), 0 0 80px rgba(239, 68, 68, 0.2); }
        }

        .status-icon::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid rgba(239, 68, 68, 0.3);
            animation: ringPulse 2s ease-in-out infinite;
        }

        @keyframes ringPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.5; }
        }

        .status-icon svg {
            width: 44px;
            height: 44px;
            color: white;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #f87171;
            font-weight: 600;
            font-size: 12px;
            padding: 6px 16px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 16px;
        }

        .badge .dot {
            width: 7px;
            height: 7px;
            background: #f87171;
            border-radius: 50%;
        }

        h1 {
            color: #f1f5f9;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .subtitle {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .warning-box {
            background: rgba(239, 68, 68, 0.06);
            border: 1px solid rgba(239, 68, 68, 0.12);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            text-align: left;
        }

        .warning-box p {
            color: #cbd5e1;
            font-size: 13px;
            line-height: 1.7;
        }

        .warning-box p strong {
            color: #f87171;
        }

        .footer-note {
            color: #475569;
            font-size: 11px;
            line-height: 1.6;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.04);
        }

        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
            color: #64748b;
            font-size: 12px;
            font-weight: 500;
        }

        .footer-logo img {
            width: 24px;
            height: 24px;
            opacity: 0.6;
        }

        @media (max-width: 480px) {
            .card {
                padding: 32px 24px;
                border-radius: 20px;
            }

            h1 {
                font-size: 19px;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <!-- Status Icon -->
        <div class="status-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </div>

        <!-- Badge -->
        <div class="badge">
            <span class="dot"></span>
            Tidak Terverifikasi
        </div>

        <!-- Title -->
        <h1>Dokumen Tidak Valid</h1>
        <p class="subtitle">
            Dokumen yang Anda coba verifikasi tidak ditemukan dalam sistem kami.
        </p>

        <!-- Warning -->
        <div class="warning-box">
            <p>
                <strong>Perhatian:</strong> QR code yang Anda pindai tidak terkait dengan dokumen resmi yang diterbitkan oleh Kecamatan Pasirjambu. Dokumen ini mungkin <strong>tidak sah</strong> atau <strong>telah dipalsukan</strong>.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer-note">
            <div class="footer-logo">
                <img src="{{ asset('images/logo_kab_bandung.png') }}" alt="Logo">
                Kecamatan Pasirjambu — Kab. Bandung
            </div>
            Jika Anda merasa ini adalah kesalahan, silakan hubungi kantor Kecamatan Pasirjambu untuk konfirmasi lebih lanjut.
        </div>
    </div>
</body>

</html>
