<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen - Kecamatan Pasirjambu</title>
    <meta name="description" content="Halaman verifikasi keaslian dokumen resmi Kecamatan Pasirjambu, Kabupaten Bandung">
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

        /* Animated background particles */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 50%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.06) 0%, transparent 50%),
                        radial-gradient(circle at 40% 80%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
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
            max-width: 520px;
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

        /* Success checkmark */
        .status-icon {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, #059669, #10b981);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            position: relative;
            animation: iconPulse 2s ease-in-out infinite;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.3),
                        0 0 60px rgba(16, 185, 129, 0.1);
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 0 30px rgba(16, 185, 129, 0.3), 0 0 60px rgba(16, 185, 129, 0.1); }
            50% { box-shadow: 0 0 40px rgba(16, 185, 129, 0.5), 0 0 80px rgba(16, 185, 129, 0.2); }
        }

        .status-icon::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid rgba(16, 185, 129, 0.3);
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
            animation: checkDraw 0.5s ease-out 0.3s both;
        }

        @keyframes checkDraw {
            from { stroke-dashoffset: 50; opacity: 0; }
            to { stroke-dashoffset: 0; opacity: 1; }
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #34d399;
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
            background: #34d399;
            border-radius: 50%;
            animation: dotBlink 1.5s ease-in-out infinite;
        }

        @keyframes dotBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
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

        /* Document details card */
        .details-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            text-align: left;
        }

        .details-card .section-title {
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .details-card .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(100, 116, 139, 0.3), transparent);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-label {
            color: #94a3b8;
            font-size: 13px;
            font-weight: 400;
            flex-shrink: 0;
            margin-right: 16px;
        }

        .detail-value {
            color: #e2e8f0;
            font-size: 13px;
            font-weight: 600;
            text-align: right;
            word-break: break-word;
        }

        .detail-value.highlight {
            color: #34d399;
        }

        /* Signature section */
        .signature-section {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.06), rgba(59, 130, 246, 0.04));
            border: 1px solid rgba(16, 185, 129, 0.12);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .signature-section .signer-name {
            color: #f1f5f9;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .signature-section .signer-title {
            color: #94a3b8;
            font-size: 13px;
        }

        .signature-section .signer-nip {
            color: #64748b;
            font-size: 12px;
            margin-top: 2px;
        }

        .signed-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 6px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .signed-badge svg {
            width: 14px;
            height: 14px;
        }

        /* Footer */
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

        /* Responsive */
        @media (max-width: 480px) {
            .card {
                padding: 32px 24px;
                border-radius: 20px;
            }

            h1 {
                font-size: 19px;
            }

            .detail-row {
                flex-direction: column;
                gap: 4px;
            }

            .detail-value {
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <!-- Status Icon -->
        <div class="status-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6L9 17l-5-5" style="stroke-dasharray: 50;" />
            </svg>
        </div>

        <!-- Badge -->
        <div class="badge">
            <span class="dot"></span>
            Dokumen Terverifikasi
        </div>

        <!-- Title -->
        <h1>Tanda Tangan Digital Sah</h1>
        <p class="subtitle">
            Dokumen ini telah ditandatangani secara resmi oleh pejabat yang berwenang di Kecamatan Pasirjambu.
        </p>

        <!-- Document Details -->
        <div class="details-card">
            <div class="section-title">Detail Dokumen</div>

            <div class="detail-row">
                <span class="detail-label">Jenis Surat</span>
                <span class="detail-value">{{ $permohonan->jenis_layanan }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Nomor Surat</span>
                <span class="detail-value">{{ $permohonan->nomor_surat ?? '-' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Nomor Antrean</span>
                <span class="detail-value">{{ $permohonan->no_antrean }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value highlight">
                    @if($permohonan->status === 'ditandatangani')
                        ✓ Ditandatangani
                    @elseif($permohonan->status === 'selesai')
                        ✓ Selesai
                    @endif
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Tanggal Ditandatangani</span>
                <span class="detail-value">{{ $permohonan->updated_at->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signed-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Ditandatangani Oleh
            </div>
            <div class="signer-name">{{ $camat->name ?? 'Camat Pasirjambu' }}</div>
            <div class="signer-title">Camat Pasirjambu — Penata Tingkat I</div>
            <div class="signer-nip">NIP. {{ $camat->nip ?? '-' }}</div>
        </div>

        <!-- Footer -->
        <div class="footer-note">
            <div class="footer-logo">
                <img src="{{ asset('images/logo_kab_bandung.png') }}" alt="Logo">
                Kecamatan Pasirjambu — Kab. Bandung
            </div>
            Dokumen ini diterbitkan secara elektronik oleh Sistem Pelayanan Administrasi Kecamatan Pasirjambu dan tidak memerlukan tanda tangan basah.
        </div>
    </div>
</body>

</html>
