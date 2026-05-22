<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Ijin Keramaian - {{ $permohonan->no_antrean }}</title>
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
            width: 100%;
            max-width: 215mm;
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
            font-size: 11pt;
            line-height: 1.5;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        @media screen and (max-width: 640px) {
            .surat-container {
                padding: 5mm 10mm;
            }
            .kop-text h2 {
                font-size: 12pt;
            }
            .kop-text h1 {
                font-size: 14pt;
            }
            .kop-text p {
                font-size: 7pt;
            }
            .kop-logo-aside, .kop-spacer, .kop-logo {
                width: 60px;
            }
            table td {
                word-break: break-word;
            }
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

        .list-numbered {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.25rem;
        }

        .list-numbered span:first-child {
            min-width: 20px;
        }
    </style>
</head>

<body class="bg-gray-100 py-10">

    <!-- Action Bar -->
    <div
        class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 p-3 sm:p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center shadow-sm no-print print:hidden z-50 font-sans gap-3 sm:gap-0">
        <h1 class="font-bold text-gray-700 items-center flex flex-wrap gap-2 text-sm sm:text-base">
            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs uppercase whitespace-nowrap">Preview</span>
            Surat Keterangan Ijin Keramaian
        </h1>
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <button onclick="window.close()"
                class="flex-1 sm:flex-none justify-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                Tutup
            </button>
            <button onclick="window.print()"
                class="flex-1 sm:flex-none justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                <span class="whitespace-nowrap">Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <div class="surat-container relative mt-28 sm:mt-20 print:mt-0">
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
        <div class="text-justify leading-relaxed">
            <div class="text-center mb-6">
                <h3 class="font-bold underline uppercase text-lg">Surat Keterangan Ijin Keramaian</h3>
                <p class="font-bold">NOMOR : {{ $permohonan->nomor_surat ?? '....................................' }}
                </p>
            </div>

            <p class="mb-4">
                Yang bertanda tangan di bawah ini Camat Pasirjambu Kabupaten Bandung, dalam rangka memenuhi permohonan
                ijin Keramaian dari:
            </p>

            <!-- Data Pemohon -->
            <div class="mb-4">
                <table class="ml-4">
                    <tr>
                        <td class="w-2">1.</td>
                        <td class="w-48 pl-2">Nama Lengkap</td>
                        <td class="w-4">:</td>
                        <td class="font-bold uppercase">
                            {{ $permohonan->metadata['pemohon']['nama'] ?? '................................................' }}
                        </td>
                    </tr>
                    <tr>
                        <td>2.</td>
                        <td class="pl-2">Tempat Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $permohonan->metadata['pemohon']['ttl'] ?? '................................................' }}
                        </td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td class="pl-2">Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $permohonan->metadata['pemohon']['gender'] ?? '................................................' }}
                        </td>
                    </tr>
                    <tr>
                        <td>4.</td>
                        <td class="pl-2">NIK</td>
                        <td>:</td>
                        <td>{{ $permohonan->metadata['pemohon']['nik'] ?? '................................................' }}
                        </td>
                    </tr>
                    <tr>
                        <td>5.</td>
                        <td class="pl-2">Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $permohonan->metadata['pemohon']['pekerjaan'] ?? '................................................' }}
                        </td>
                    </tr>
                    <tr>
                        <td>6.</td>
                        <td class="pl-2">Alamat</td>
                        <td>:</td>
                        <td>{{ $permohonan->metadata['pemohon']['alamat'] ?? '................................................' }}
                        </td>
                    </tr>
                </table>
            </div>

            <p class="mb-4">
                Bahwa nama tersebut di atas bermaksud akan mengadakan keramaian, yang akan diadakan pada:
            </p>

            <!-- Data Keramaian -->
            <div class="mb-4">
                <table class="ml-4">
                    <tr>
                        <td class="w-2">1.</td>
                        <td class="w-48 pl-2">Hari/Tanggal</td>
                        <td class="w-4">:</td>
                        <td>{{ $permohonan->metadata['keramaian']['tanggal'] ?? '................................................' }}
                        </td>
                    </tr>
                    <tr>
                        <td>2.</td>
                        <td class="pl-2">Acara</td>
                        <td>:</td>
                        <td>{{ $permohonan->metadata['keramaian']['acara'] ?? '................................................' }}
                        </td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td class="pl-2">Lokasi</td>
                        <td>:</td>
                        <td>{{ $permohonan->metadata['keramaian']['lokasi'] ?? '................................................' }}
                        </td>
                    </tr>
                    <tr>
                        <td>4.</td>
                        <td class="pl-2">Hiburan</td>
                        <td>:</td>
                        <td>{{ $permohonan->metadata['keramaian']['hiburan'] ?? '................................................' }}
                        </td>
                    </tr>
                </table>
            </div>

            <p class="mb-4">
                Dengan ini menerangkan, pada prinsipnya tidak keberatan atas permohonan yang bersangkutan dengan
                ketentuan sebagai berikut:
            </p>

            <!-- Ketentuan -->
            <div class="mb-6 space-y-2">
                <div class="flex gap-2">
                    <span class="min-w-[20px]">1.</span>
                    <p class="text-justify">Pada waktu dilaksanakan rame-rame harus menjaga ketentraman dan ketertiban
                        dalam lingkungan, baik hubungan dengan tetangga, menghargai waktu ibadah dalam menciptakan
                        kerukunan umat beragama maupun kebersihan lingkungan setelah selesai rame-rame.</p>
                </div>
                <div class="flex gap-2">
                    <span class="min-w-[20px]">2.</span>
                    <p class="text-justify">Pada waktu dilaksanakan rame-rame tidak dibenarkan melakukan hal-hal yang
                        bertentangan dengan ketentuan yang berlaku dan adat istiadat setempat.</p>
                </div>
            </div>

            <p class="mb-8">
                Demikian, surat keterangan ini diberikan untuk digunakan sebagaimana mestinya.
            </p>

            <!-- Tanda Tangan -->
            <div class="flex justify-end mt-8">
                <div class="text-center" style="width: 8.5cm;">
                    <p class="text-left mb-1">Pasirjambu, Tanggal
                        {{ $permohonan->updated_at->translatedFormat('d F Y') }}
                    </p>
                    <p class="font-bold uppercase mb-1">Camat Pasirjambu</p>

                    <div class="flex justify-center items-center h-24 my-1">
                        @if($permohonan->status == 'ditandatangani' || $permohonan->status == 'selesai')
                            <canvas id="qrcode-canvas"></canvas>
                        @else
                            <div class="h-24 w-24"></div>
                        @endif
                    </div>

                    <p class="font-bold underline uppercase">{{ $camat->name }}</p>
                    <p>Penata Tingkat I</p>
                    <p>NIP. {{ $camat->nip ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($permohonan->status == 'ditandatangani' || $permohonan->status == 'selesai')
        <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
        <script>
            QRCode.toCanvas(document.getElementById('qrcode-canvas'), '{{ route('dokumen.verify', $permohonan->verification_token ?? 'belum-diverifikasi') }}', {
                width: 80,
                margin: 1
            }, function (error) {
                if (error) console.error(error)
            })
        </script>
    @endif

</body>

</html>