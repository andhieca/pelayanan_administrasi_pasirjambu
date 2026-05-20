<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draft Surat Dispensasi Nikah - {{ $permohonan->no_antrean }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: 215mm 330mm;
                /* F4 / Folio Size */
                margin: 0;
            }

            body {
                -webkit-print-color-adjust: exact;
                margin: 0;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }
        }

        .surat-container {
            width: 215mm;
            /* F4 Width */
            min-height: 330mm;
            /* F4 Height */
            padding: 5mm 25mm;
            /* Adjusted margins closer to standard letter */
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: 'Times New Roman', Times, serif;
            /* Generic serif for legal docs */
            font-size: 12pt;
        }

        table {
            width: 100%;
        }

        td {
            padding: 1px 0;
            vertical-align: top;
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
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .kop-text h1 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 20pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .kop-text p {
            font-family: Arial, Helvetica, sans-serif;
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
    </style>
</head>

<body class="bg-gray-100 py-10">

    <!-- Action Bar -->
    <div
        class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 p-4 flex justify-between items-center shadow-sm no-print print:hidden z-50 font-sans">
        <h1 class="font-bold text-gray-700 items-center flex gap-2">
            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs uppercase">Draft</span>
            Preview Surat Dispensasi Nikah
        </h1>
        <div class="flex gap-2">
            <button onclick="window.close()"
                class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                Tutup
            </button>
            <button onclick="window.print()"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="surat-container relative mt-20 print:mt-0">
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

        <!-- Body Surat -->
        <div class="text-justify leading-snug">
            <div class="text-center mb-6">
                <h3 class="font-bold underline uppercase">Surat Dispensasi Nikah</h3>
                <p>Nomor: {{ $permohonan->nomor_surat ?? 'PR.06.02/' . substr($permohonan->no_antrean, -3) . '/P/M' }}
                </p>
            </div>

            <p class="mb-4 indent-8">
                Berdasarkan Peraturan Menteri Agama Republik Indonesia Nomor 30 Tahun 2024 Tentang Pencatatan Nikah
                menurut Bab II Pasal 3 ayat 2 bahwa kami sebagai Camat Pasirjambu Kabupaten Bandung memberikan
                rekomendasi kepada :
            </p>

            <!-- 1. Calon Istri -->
            <div class="mb-4">
                <p class="font-bold">1. CALON ISTRI</p>
                <table class="ml-4">
                    <tr>
                        <td class="w-48">Nama Lengkap</td>
                        <td class="w-4">:</td>
                        <td class="font-bold">
                            {{ json_decode(json_encode($permohonan->metadata['istri']), true)['nama'] ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['istri']), true)['nik'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Binti</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['istri']), true)['binti'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat/Tgl. Lahir</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['istri']), true)['ttl'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['istri']), true)['agama'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['istri']), true)['pekerjaan'] ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['istri']), true)['status'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['istri']), true)['alamat'] ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <!-- 2. Calon Suami -->
            <div class="mb-4">
                <p class="font-bold">2. CALON SUAMI</p>
                <table class="ml-4">
                    <tr>
                        <td class="w-48">Nama Lengkap</td>
                        <td class="w-4">:</td>
                        <td class="font-bold">
                            {{ json_decode(json_encode($permohonan->metadata['suami']), true)['nama'] ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['suami']), true)['nik'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Bin</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['suami']), true)['bin'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat/Tgl. Lahir</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['suami']), true)['ttl'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['suami']), true)['agama'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['suami']), true)['pekerjaan'] ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['suami']), true)['status'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ json_decode(json_encode($permohonan->metadata['suami']), true)['alamat'] ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <p class="mb-2">Untuk melakukan perkawinan / pernikahan pada :</p>
            <table class="ml-4 mb-4">
                <tr>
                    <td class="w-48">Hari</td>
                    <td class="w-4">:</td>
                    <td>{{ json_decode(json_encode($permohonan->metadata['pernikahan']), true)['hari'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>:</td>
                    <td>{{ json_decode(json_encode($permohonan->metadata['pernikahan']), true)['waktu'] ?? '-' }} WIB
                    </td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse(json_decode(json_encode($permohonan->metadata['pernikahan']), true)['tanggal'])->translatedFormat('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td>Tempat Akad</td>
                    <td>:</td>
                    <td>{{ json_decode(json_encode($permohonan->metadata['pernikahan']), true)['tempat'] ?? '-' }}</td>
                </tr>
            </table>

            <p class="mb-4 text-justify">
                Berdasarkan atas permintaan sendiri dari yang bersangkutan karena pelaksanaan kurang dari 10 (sepuluh)
                hari dan keterlambatannya disebabkan yang bersangkutan masih mengurus persyaratan administrasi.
            </p>

            <!-- Tanda Tangan -->
            <div class="flex justify-end mt-8">
                <div class="text-center" style="width: 8.5cm;">
                    <p class="text-left mb-1">Pasirjambu, Tanggal
                        {{ $permohonan->updated_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
                    </p>
                    <p class="font-bold uppercase mb-1">Camat Pasirjambu</p>

                    <div class="flex justify-center items-center h-24 my-1">
                        @if($permohonan->status == 'ditandatangani' || $permohonan->status == 'selesai')
                            <canvas id="qrcode-canvas"></canvas>
                        @endif
                    </div>

                    <p class="font-bold underline uppercase">{{ $camat->name }}</p>
                    <p>Penata Tingkat I</p>
                    <p>NIP. {{ $camat->nip ?? '-' }}</p>
                </div>
            </div>

            <div class="clear-both"></div>
        </div>
    </div>

    @if($permohonan->status == 'ditandatangani' || $permohonan->status == 'selesai')
        <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
        <script>
            QRCode.toCanvas(document.getElementById('qrcode-canvas'), '{{ route('dokumen.verify', $permohonan->verification_token ?? 'belum-diverifikasi') }}', {
                width: 90,
                margin: 1
            }, function (error) {
                if (error) console.error(error)
            })
        </script>
    @endif

</body>

</html>