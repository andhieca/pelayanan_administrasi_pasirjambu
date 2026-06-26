<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Pelayanan - Camat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4; /* Using A4 size for standard reports */
                margin: 0;
            }

            body {
                -webkit-print-color-adjust: exact;
                margin: 0;
                padding: 0 !important;
                background-color: white !important;
            }

            .no-print {
                display: none !important;
            }
            
            .surat-container {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 10mm 15mm !important;
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
            }
            
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }

        .surat-container {
            width: 100%;
            max-width: 210mm; /* A4 width */
            min-height: 297mm; /* A4 height */
            padding: 15mm 20mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            box-sizing: border-box;
        }

        .kop-surat-container {
            display: flex;
            align-items: center;
            margin-bottom: 2mm;
        }

        .kop-logo-aside {
            width: 90px;
            flex-shrink: 0;
        }

        .kop-logo {
            width: 90px;
            height: auto;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-spacer {
            width: 90px;
            flex-shrink: 0;
        }

        .kop-text h2 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .kop-text h1 {
            font-size: 20pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .kop-text p {
            font-size: 8pt;
            margin: 0;
            line-height: 1.2;
        }

        .kop-separator {
            border-top: 3px solid black;
            border-bottom: 1px solid black;
            height: 4px;
            margin-top: 4px;
            margin-bottom: 15px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            vertical-align: top;
        }
        
        table.data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: left;
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>

<body class="bg-slate-100 py-10 flex flex-col items-center">

    <!-- Action Bar -->
    <div class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 p-3 sm:p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center shadow-sm no-print print:hidden z-50 font-sans gap-3 sm:gap-0 w-full">
        <h1 class="font-bold text-slate-700 flex items-center gap-2 text-sm sm:text-base">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Laporan Pelayanan Administrasi
        </h1>
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <button onclick="window.close()"
                class="flex-1 sm:flex-none justify-center px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                Tutup
            </button>
            <button onclick="window.print()"
                class="flex-1 sm:flex-none justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-md">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                <span class="whitespace-nowrap">Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <div class="surat-container mt-28 sm:mt-20 print:mt-0 relative shrink-0">
        <!-- Kop Surat -->
        <div class="kop-surat-container">
            <div class="kop-logo-aside">
                <img src="{{ asset('images/logo_kab_bandung.png') }}" class="kop-logo" alt="Logo">
            </div>
            <div class="kop-text">
                <h2>PEMERINTAH KABUPATEN BANDUNG</h2>
                <h1>KECAMATAN PASIRJAMBU</h1>
                <p>Jalan Lapang Jenderal No.100 Telp/ Fax. (022) 5927477 Pasirjambu</p>
                <p>Kabupaten Bandung Provinsi Jawa Barat website: www.kecamatanpasirjambu@bandungkab.go.id</p>
                <p>e-mail: kec.pasirjambu@bandungkab.go.id</p>
            </div>
            <div class="kop-spacer"></div>
        </div>
        <div class="kop-separator"></div>

        <!-- Judul Laporan -->
        <div class="text-center mt-6 mb-8">
            <h3 class="font-bold uppercase text-lg">Laporan Rekapitulasi Pelayanan Administrasi</h3>
            <p class="text-sm mt-1">Status Permohonan: Disetujui & Ditolak</p>
        </div>
        
        <!-- Ringkasan Statistik -->
        <div class="mb-6 flex gap-8">
            <div>
                <p class="text-xs uppercase font-bold text-gray-500 mb-1">Total Diproses</p>
                <p class="font-bold text-xl">{{ $total }}</p>
            </div>
            <div>
                <p class="text-xs uppercase font-bold text-gray-500 mb-1">Total Disetujui</p>
                <p class="font-bold text-xl text-green-700">{{ $disetujui }}</p>
            </div>
            <div>
                <p class="text-xs uppercase font-bold text-gray-500 mb-1">Total Ditolak</p>
                <p class="font-bold text-xl text-red-700">{{ $ditolak }}</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-xs uppercase font-bold text-gray-500 mb-1">Tanggal Cetak</p>
                <p class="font-bold">{{ date('d M Y, H:i') }}</p>
            </div>
        </div>

        <!-- Tabel Data -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 15%;">No Antrean</th>
                    <th style="width: 25%;">Pemohon</th>
                    <th style="width: 25%;">Layanan</th>
                    <th style="width: 15%;">Tgl Proses</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $index => $h)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">#{{ $h->no_antrean }}</td>
                    <td>{{ $h->user->name }}</td>
                    <td>{{ $h->jenis_layanan }}</td>
                    <td>{{ $h->updated_at->format('d/m/Y') }}</td>
                    <td>
                        @if($h->status == 'ditandatangani' || $h->status == 'selesai')
                            <span style="color: #047857; font-weight: bold;">Disetujui</span>
                        @else
                            <span style="color: #b91c1c; font-weight: bold;">Ditolak</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; font-style: italic; color: #666;">Belum ada riwayat permohonan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div style="display: flex; justify-content: flex-end; margin-top: 50px; padding-right: 30px;">
            <div style="text-align: center; width: 7cm;">
                <p style="margin-bottom: 5px;">Pasirjambu, {{ date('d F Y') }}</p>
                <p style="text-transform: uppercase; margin-bottom: 70px;">CAMAT PASIRJAMBU</p>
                
                <p style="font-weight: bold; text-decoration: underline; text-transform: uppercase;">{{ $camat->name }}</p>
                <p>NIP. {{ $camat->nip ?? '-' }}</p>
            </div>
        </div>

    </div>

</body>
</html>
