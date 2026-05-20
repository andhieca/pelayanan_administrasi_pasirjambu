<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Rekomendasi Bantuan - {{ $permohonan->no_antrean }}</title>
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
            font-family: Arial, Helvetica, sans-serif;
            /* Set font to Arial */
            font-size: 11pt;
            line-height: 1.5;
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

<body class="bg-gray-100 py-10 flex flex-col items-center">

    @if(Route::currentRouteName() !== 'camat.preview')
        <!-- Action Bar -->
        <div
            class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 p-4 flex justify-between items-center shadow-sm no-print print:hidden z-50 font-sans w-full">
            <h1 class="font-bold text-gray-700 items-center flex gap-2">
                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs uppercase">Preview</span>
                Surat Rekomendasi Bantuan
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
    @endif

    <div
        class="surat-container relative shrink-0 {{ Route::currentRouteName() !== 'camat.preview' ? 'mt-20' : '' }} print:mt-0">
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
        <div class="text-justify leading-relaxed mt-8">
            <div class="text-center mb-8">
                <h3 class="font-bold underline uppercase text-xl">REKOMENDASI</h3>
                <p class="">Nomor : {{ $permohonan->nomor_surat ?? '....................................' }}</p>
            </div>

            <p class="mb-4 indent-8">
                Berdasarkan Surat dan Proposal dari Kelompok
                {{ $permohonan->metadata['rekomendasi']['jenis_kelompok'] ?? '........' }}
                {{ $permohonan->metadata['rekomendasi']['nama_kelompok'] ?? '........' }} yang beralamat di
                {{ $permohonan->metadata['rekomendasi']['alamat'] ?? '........' }}
                Perihal {{ $permohonan->metadata['rekomendasi']['perihal'] ?? '........' }}.
            </p>

            <p class="mb-4 indent-8">
                Sehubungan dengan hal dimaksud di atas, pada prinsipnya kami tidak keberatan kepada Kelompok
                {{ $permohonan->metadata['rekomendasi']['jenis_kelompok'] ?? '........' }}
                {{ $permohonan->metadata['rekomendasi']['nama_kelompok'] ?? '........' }}
                yang beralamat di {{ $permohonan->metadata['rekomendasi']['alamat'] ?? '........' }} untuk mendapat
                Bantuan, sepanjang memenuhi Persyaratan sebagai berikut :
            </p>

            <div class="mb-6 space-y-2 pl-4">
                <div class="flex gap-2">
                    <span class="min-w-[20px]">1.</span>
                    <p class="text-justify">Bantuan yang diusulkan betul-betul digunakan untuk Kepentingan Kelompok
                        {{ $permohonan->metadata['rekomendasi']['jenis_kelompok'] ?? '........' }}
                        {{ $permohonan->metadata['rekomendasi']['nama_kelompok'] ?? '........' }} bukan Perorangan.
                    </p>
                </div>
                <div class="flex gap-2">
                    <span class="min-w-[20px]">2.</span>
                    <p class="text-justify">Mematuhi semua Peraturan dan Perundang-Undangan yang mengatur
                        pelaksanaan/penggunaan Bantuan tersebut.</p>
                </div>
                <div class="flex gap-2">
                    <span class="min-w-[20px]">3.</span>
                    <p class="text-justify">Bertanggung jawab sepenuhnya atas penggunaan Bantuan tersebut dan melaporkan
                        pertanggungjawaban keuangan melalui Camat Pasirjambu selambat-lambatnya 14 hari sejak tanggal
                        penerimaan bantuan.</p>
                </div>
                <div class="flex gap-2">
                    <span class="min-w-[20px]">4.</span>
                    <p class="text-justify">Apabila penggunaan bantuan tidak sesuai dengan peruntukannya, maka Kelompok
                        {{ $permohonan->metadata['rekomendasi']['jenis_kelompok'] ?? '........' }}
                        {{ $permohonan->metadata['rekomendasi']['nama_kelompok'] ?? '........' }}
                        yang beralamat di {{ $permohonan->metadata['rekomendasi']['alamat'] ?? '........' }}
                        bertanggung jawab sepenuhnya atas segala akibat hukum dan tidak akan melibatkan pihak Pemerintah
                        Desa {{ $permohonan->metadata['rekomendasi']['nama_desa'] ?? '........' }}.
                    </p>
                </div>
            </div>

            <p class="mb-8 indent-8">
                Demikian Rekomendasi ini kami buat dengan sebenarnya dan untuk dipergunakan sebagai mana mestinya.
            </p>

            <!-- Tanda Tangan -->
            <div class="flex justify-end mt-12 pr-8">
                <div class="text-center" style="width: 8.5cm;">
                    <p class="mb-1">Pasirjambu,
                        {{ $permohonan->updated_at ? $permohonan->updated_at->translatedFormat('d F Y') : date('d F Y') }}
                    </p>
                    <p class="uppercase mb-1">CAMAT PASIRJAMBU</p>

                    <div class="flex justify-center items-center h-24 my-2">
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