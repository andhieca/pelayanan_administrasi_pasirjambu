<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pelayanan Administrasi Pasirjambu</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        slate: {
                            850: '#172033', // Custom darker slate
                        },
                        bedas: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#009F4D', // Hijau Bedas (Primary)
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="antialiased bg-slate-50 text-slate-600 font-sans selection:bg-bedas-500 selection:text-white" x-data="{ 
        showLogin: {{ ($errors->has('email') && !old('name')) || $errors->has('password') || session('success') ? 'true' : 'false' }} || new URLSearchParams(window.location.search).get('auth') === 'login', 
        showRegister: {{ $errors->has('name') || ($errors->has('email') && old('name')) || $errors->has('password_confirmation') ? 'true' : 'false' }} || new URLSearchParams(window.location.search).get('auth') === 'register',
        mobileMenuOpen: false,
        toggleAuth() {
            this.showLogin = !this.showLogin;
            this.showRegister = !this.showRegister;
        }
      }" x-init="
        // Clean URL after reading query params
        if (new URLSearchParams(window.location.search).get('auth')) {
            window.history.replaceState({}, '', window.location.pathname);
        }
      ">

    <!-- Navbar -->
    <nav x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)"
        :class="{ 'bg-white/80 backdrop-blur-md shadow-sm': scrolled, 'bg-transparent': !scrolled }"
        class="fixed w-full z-50 transition-all duration-300 top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('logo-kab-bandung.png') }}" alt="Logo"
                        class="w-10 h-auto group-hover:scale-110 transition-transform duration-300">
                    <span class="font-display font-bold text-xl text-slate-900 tracking-tight">Pasirjambu</span>
                </a>
                <div class="hidden md:flex items-center gap-8 ml-10">
                    <a href="{{ url('/#layanan') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-bedas-600 transition-colors">Layanan</a>
                    <a href="{{ url('/#berita') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-bedas-600 transition-colors">Berita</a>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="text-sm font-bold text-bedas-600 hover:text-bedas-700 transition-colors">Dashboard</a>
                            @else
                                <button @click="showLogin = true"
                                    class="text-sm font-bold text-slate-600 hover:text-bedas-600 transition-colors">Log
                                    in</button>
                                @if (Route::has('register'))
                                    <button @click="showRegister = true"
                                        class="px-5 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-full hover:bg-slate-800 transition-all shadow-lg shadow-slate-200 hover:shadow-xl transform hover:-translate-y-0.5">Register</button>
                                @endif
                            @endauth
                        @endif
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 text-slate-600 hover:text-bedas-600 transition-colors focus:outline-none">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden bg-white border-t border-slate-100 py-4 px-4 shadow-xl" style="display: none;">
            <div class="flex flex-col gap-4">
                <a href="{{ url('/#layanan') }}" @click="mobileMenuOpen = false"
                    class="text-base font-semibold text-slate-600 hover:text-bedas-600 px-2 py-1">Layanan</a>
                <a href="{{ url('/#berita') }}" @click="mobileMenuOpen = false"
                    class="text-base font-semibold text-slate-600 hover:text-bedas-600 px-2 py-1">Berita</a>
                <hr class="border-slate-100">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-base font-bold text-bedas-600 px-2 py-1">Dashboard</a>
                    @else
                        <button @click="showLogin = true; mobileMenuOpen = false"
                            class="text-left text-base font-semibold text-slate-600 px-2 py-1">Log in</button>
                        <button @click="showRegister = true; mobileMenuOpen = false"
                            class="text-left text-base font-bold text-bedas-600 px-2 py-1">Register</button>
                    @endauth
                @endif
            </div>
        </div>
        </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-[90vh] flex items-center pt-20 pb-20 overflow-hidden" x-data="{ loaded: false }"
        x-init="setTimeout(() => loaded = true, 100)">
        <!-- Background with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('hero-bg.png') }}" alt="Public Service" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/95 to-white/40"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-white"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl text-left">
                <div x-show="loaded" x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 -translate-x-10"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    <span
                        class="inline-block py-1.5 px-4 rounded-full bg-bedas-100 text-bedas-700 text-xs font-bold uppercase tracking-widest mb-6 shadow-sm">
                        Pelayanan Digital Terpadu
                    </span>
                </div>

                <h1 class="text-5xl md:text-7xl font-display font-extrabold text-slate-900 leading-[1.1] mb-8"
                    x-show="loaded" x-transition:enter="transition ease-out duration-1000 delay-300"
                    x-transition:enter-start="opacity-0 translate-y-10"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    Pengurusan Administrasi <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-bedas-600 to-emerald-500">
                        Lebih Mudah & Cepat
                    </span>
                </h1>

                <p class="text-xl text-slate-600 mb-12 max-w-xl leading-relaxed" x-show="loaded"
                    x-transition:enter="transition ease-out duration-1000 delay-500"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    Platform digital resmi Kecamatan Pasirjambu untuk layanan dispensasi nikah, rekomendasi bantuan, dan
                    izin keramaian tanpa antre lama.
                </p>

                <div class="flex flex-col sm:flex-row gap-5" x-show="loaded"
                    x-transition:enter="transition ease-out duration-1000 delay-700"
                    x-transition:enter-start="opacity-0 translate-y-5"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-10 py-4 bg-bedas-600 text-white font-bold rounded-2xl shadow-2xl shadow-bedas-200 hover:bg-bedas-700 hover:shadow-bedas-300 transition-all transform hover:-translate-y-1 text-center">
                            Masuk ke Dashboard
                        </a>
                    @else
                        <button @click="showRegister = true"
                            class="px-10 py-4 bg-bedas-600 text-white font-bold rounded-2xl shadow-2xl shadow-bedas-200 hover:bg-bedas-700 hover:shadow-bedas-300 transition-all transform hover:-translate-y-1 text-center">
                            Buat Permohonan Sekarang
                        </button>
                    @endauth
                    <a href="{{ url('/#layanan') }}"
                        class="px-10 py-4 bg-white/80 backdrop-blur-md text-slate-700 font-bold rounded-2xl border border-slate-200 shadow-xl hover:bg-white transition-all text-center">
                        Lihat Layanan
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features / Services -->
    <section id="layanan" class="py-24 bg-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20" x-data="{ shown: false }" x-intersect="shown = true">
                <h2 class="text-4xl font-display font-extrabold text-slate-900 mb-6" x-show="shown"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    Layanan Kami
                </h2>
                <div class="w-20 h-1.5 bg-bedas-600 mx-auto rounded-full mb-8" x-show="shown"
                    x-transition:enter="transition ease-out duration-700 delay-200"
                    x-transition:enter-start="opacity-0 scale-x-0" x-transition:enter-end="opacity-100 scale-x-100">
                </div>
                <p class="text-slate-500 max-w-xl mx-auto text-lg leading-relaxed" x-show="shown"
                    x-transition:enter="transition ease-out duration-700 delay-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100">
                    Kami menyediakan berbagai layanan administrasi yang dapat diakses secara online dengan proses yang
                    transparan dan akuntabel.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-10" x-data="{ 
                shown: false, 
                showServiceDetail: false, 
                activeService: null,
                openService(service) {
                    this.activeService = service;
                    this.showServiceDetail = true;
                    document.body.style.overflow = 'hidden';
                },
                closeService() {
                    this.showServiceDetail = false;
                    document.body.style.overflow = 'auto';
                }
            }" x-intersect="shown = true">
                <!-- Card 1 -->
                <div x-show="shown" x-transition:enter="transition ease-out duration-700 delay-[200ms]"
                    x-transition:enter-start="opacity-0 translate-y-10"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    @click="openService('dispen')"
                    class="cursor-pointer group p-10 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-white hover:border-bedas-200 hover:shadow-[0_20px_50px_rgba(0,159,77,0.1)] transition-all duration-500 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-bedas-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-0">
                    </div>
                    <div
                        class="relative z-10 text-4xl mb-8 w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm group-hover:scale-110 group-hover:bg-bedas-600 group-hover:text-white transition-all duration-500">
                        💍
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4 group-hover:text-bedas-700 transition-colors">
                        Dispensasi Nikah</h3>
                    <p class="text-slate-500 leading-relaxed text-lg">Pengurusan surat keterangan dispensasi nikah
                        dengan sistem validasi data yang cepat dan akurat.</p>
                    <div class="mt-6 flex items-center gap-2 text-bedas-600 font-semibold text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span>Lihat Persyaratan</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>

                <!-- Card 2 -->
                <div x-show="shown" x-transition:enter="transition ease-out duration-700 delay-[400ms]"
                    x-transition:enter-start="opacity-0 translate-y-10"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    @click="openService('rekomendasi')"
                    class="cursor-pointer group p-10 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-white hover:border-bedas-200 hover:shadow-[0_20px_50px_rgba(0,159,77,0.1)] transition-all duration-500 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-bedas-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-0">
                    </div>
                    <div
                        class="relative z-10 text-4xl mb-8 w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm group-hover:scale-110 group-hover:bg-bedas-600 group-hover:text-white transition-all duration-500">
                        🤝
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4 group-hover:text-bedas-700 transition-colors">
                        Rekomendasi Bantuan</h3>
                    <p class="text-slate-500 leading-relaxed text-lg">Permohonan surat rekomendasi untuk berbagai
                        bantuan sosial dan program daerah tepat sasaran.</p>
                    <div class="mt-6 flex items-center gap-2 text-bedas-600 font-semibold text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span>Lihat Persyaratan</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>

                <!-- Card 3 -->
                <div x-show="shown" x-transition:enter="transition ease-out duration-700 delay-[600ms]"
                    x-transition:enter-start="opacity-0 translate-y-10"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    @click="openService('keramaian')"
                    class="cursor-pointer group p-10 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-white hover:border-bedas-200 hover:shadow-[0_20px_50px_rgba(0,159,77,0.1)] transition-all duration-500 relative overflow-hidden">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-bedas-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-0">
                    </div>
                    <div
                        class="relative z-10 text-4xl mb-8 w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm group-hover:scale-110 group-hover:bg-bedas-600 group-hover:text-white transition-all duration-500">
                        🎉
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4 group-hover:text-bedas-700 transition-colors">Izin
                        Keramaian</h3>
                    <p class="text-slate-500 leading-relaxed text-lg">Urus perizinan untuk acara keramaian di lingkungan
                        masyarakat dengan sistem pelaporan terpadu.</p>
                    <div class="mt-6 flex items-center gap-2 text-bedas-600 font-semibold text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span>Lihat Persyaratan</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>

                <!-- Service Detail Modal -->
                <div x-show="showServiceDetail" class="fixed inset-0 z-[150] overflow-y-auto" style="display: none;" x-cloak>
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Backdrop -->
                        <div x-show="showServiceDetail" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" @click="closeService()"
                            class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-md"></div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                        <!-- Modal Content -->
                        <div x-show="showServiceDetail" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="inline-block w-full max-w-3xl overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[32px] sm:my-8">

                            <!-- Modal Header -->
                            <div class="relative px-8 sm:px-10 pt-8 pb-6 border-b border-slate-100">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shadow-sm"
                                            :class="{
                                                'bg-blue-50': activeService === 'dispen',
                                                'bg-emerald-50': activeService === 'rekomendasi',
                                                'bg-violet-50': activeService === 'keramaian'
                                            }">
                                            <span x-text="activeService === 'dispen' ? '💍' : (activeService === 'rekomendasi' ? '🤝' : '🎉')"></span>
                                        </div>
                                        <div>
                                            <h2 class="text-2xl font-display font-bold text-slate-900"
                                                x-text="activeService === 'dispen' ? 'Dispensasi Nikah' : (activeService === 'rekomendasi' ? 'Rekomendasi Bantuan' : 'Izin Keramaian')"></h2>
                                            <p class="text-slate-500 text-sm mt-1">Detail persyaratan permohonan</p>
                                        </div>
                                    </div>
                                    <button @click="closeService()"
                                        class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all focus:outline-none">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Body -->
                            <div class="px-8 sm:px-10 py-8 overflow-y-auto max-h-[65vh] space-y-8">

                                <!-- ======================== DISPEN NIKAH ======================== -->
                                <template x-if="activeService === 'dispen'">
                                    <div class="space-y-6">
                                        <!-- Ringkasan -->
                                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-blue-900 text-sm">Tentang Layanan</h4>
                                                    <p class="text-blue-700 text-sm mt-1 leading-relaxed">Pelayanan pengajuan surat dispensasi nikah untuk pasangan calon pengantin yang memerlukan surat keterangan dari kecamatan.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Data yang Perlu Diisi -->
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                                <span class="w-7 h-7 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center text-xs">📝</span>
                                                Data yang Perlu Diisi
                                            </h3>
                                            <div class="space-y-4">
                                                <!-- Calon Suami -->
                                                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                                                    <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                                                        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center text-xs">👨</span>
                                                        Data Calon Suami
                                                    </h4>
                                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Nama Lengkap
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            NIK
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Bin
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            TTL
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Agama
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Pekerjaan
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Status
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Alamat
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- Calon Istri -->
                                                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                                                    <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                                                        <span class="w-6 h-6 bg-pink-100 text-pink-600 rounded-md flex items-center justify-center text-xs">👩</span>
                                                        Data Calon Istri
                                                    </h4>
                                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Nama Lengkap
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            NIK
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Binti
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            TTL
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Agama
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Pekerjaan
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Status
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Alamat
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- Rencana Pernikahan -->
                                                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                                                    <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                                                        <span class="w-6 h-6 bg-purple-100 text-purple-600 rounded-md flex items-center justify-center text-xs">📅</span>
                                                        Rencana Pernikahan
                                                    </h4>
                                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Hari
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Tanggal
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Waktu
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Tempat Akad
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- Lainnya -->
                                                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                                                    <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                                                        <span class="w-6 h-6 bg-green-100 text-green-600 rounded-md flex items-center justify-center text-xs">📝</span>
                                                        Lainnya
                                                    </h4>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Alasan Pengajuan
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Nomor WhatsApp
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Berkas Persyaratan -->
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                                <span class="w-7 h-7 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs">📂</span>
                                                Berkas Persyaratan (Wajib Upload)
                                            </h3>
                                            <div class="bg-slate-50 rounded-xl border border-slate-100 divide-y divide-slate-100">
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                                                    <span class="text-sm text-slate-700 font-medium">KTP Calon Istri</span>
                                                </div>
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                                                    <span class="text-sm text-slate-700 font-medium">KTP Calon Suami</span>
                                                </div>
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                                                    <span class="text-sm text-slate-700 font-medium">KK Calon Istri</span>
                                                </div>
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">4</span>
                                                    <span class="text-sm text-slate-700 font-medium">KK Calon Suami</span>
                                                </div>
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">5</span>
                                                    <span class="text-sm text-slate-700 font-medium">Pas Foto Latar Biru</span>
                                                </div>
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">6</span>
                                                    <span class="text-sm text-slate-700 font-medium">N1 (Desa Calon Istri)</span>
                                                </div>
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">7</span>
                                                    <span class="text-sm text-slate-700 font-medium">N1 (Desa/Kecamatan Calon Suami - Jika Beda)</span>
                                                </div>
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">8</span>
                                                    <span class="text-sm text-slate-700 font-medium">N2 Permohonan Kehendak Nikah</span>
                                                </div>
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">9</span>
                                                    <span class="text-sm text-slate-700 font-medium">N4 Persetujuan Pengantin</span>
                                                </div>
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">10</span>
                                                    <span class="text-sm text-slate-700 font-medium">N10 Rekomendasi KUA</span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-slate-400 mt-3 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Format file yang diterima: PDF atau Gambar (JPG, PNG)
                                            </p>
                                        </div>
                                    </div>
                                </template>

                                <!-- ======================== REKOMENDASI BANTUAN ======================== -->
                                <template x-if="activeService === 'rekomendasi'">
                                    <div class="space-y-6">
                                        <!-- Ringkasan -->
                                        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-emerald-900 text-sm">Tentang Layanan</h4>
                                                    <p class="text-emerald-700 text-sm mt-1 leading-relaxed">Pelayanan surat rekomendasi untuk pengajuan berbagai bantuan sosial dan program daerah bagi kelompok masyarakat.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Data yang Perlu Diisi -->
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                                <span class="w-7 h-7 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center text-xs">📝</span>
                                                Data yang Perlu Diisi
                                            </h3>
                                            <div class="space-y-4">
                                                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                                                    <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                                                        <span class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-md flex items-center justify-center text-xs">📋</span>
                                                        Data Rekomendasi Bantuan
                                                    </h4>
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Jenis Kelompok
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Nama Kelompok
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Alamat Lengkap
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Perihal
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Nama Desa
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Berkas Persyaratan -->
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                                <span class="w-7 h-7 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs">📂</span>
                                                Berkas Persyaratan (Wajib Upload)
                                            </h3>
                                            <div class="bg-slate-50 rounded-xl border border-slate-100 divide-y divide-slate-100">
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-emerald-100 text-emerald-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                                                    <span class="text-sm text-slate-700 font-medium">Proposal Bantuan</span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-slate-400 mt-3 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Format file yang diterima: PDF atau Gambar (JPG, PNG)
                                            </p>
                                        </div>
                                    </div>
                                </template>

                                <!-- ======================== IZIN KERAMAIAN ======================== -->
                                <template x-if="activeService === 'keramaian'">
                                    <div class="space-y-6">
                                        <!-- Ringkasan -->
                                        <div class="bg-violet-50 border border-violet-100 rounded-2xl p-5">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 bg-violet-100 text-violet-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-violet-900 text-sm">Tentang Layanan</h4>
                                                    <p class="text-violet-700 text-sm mt-1 leading-relaxed">Pelayanan perizinan untuk mengadakan acara keramaian di lingkungan masyarakat dengan sistem pelaporan terpadu.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Data yang Perlu Diisi -->
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                                <span class="w-7 h-7 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center text-xs">📝</span>
                                                Data yang Perlu Diisi
                                            </h3>
                                            <div class="space-y-4">
                                                <!-- Data Pemohon -->
                                                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                                                    <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                                                        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center text-xs">👤</span>
                                                        Data Pemohon
                                                    </h4>
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Nama Lengkap
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            NIK
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Tempat, Tanggal Lahir
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Jenis Kelamin
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Pekerjaan
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Alamat
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- Detail Keramaian -->
                                                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                                                    <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                                                        <span class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-md flex items-center justify-center text-xs">🎉</span>
                                                        Maksud Mengadakan Keramaian
                                                    </h4>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Hari / Tanggal
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Acara
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Lokasi
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-white rounded-lg border border-slate-100 text-xs text-slate-600 font-medium">
                                                            <svg class="w-3 h-3 text-bedas-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Hiburan
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Berkas Persyaratan -->
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                                <span class="w-7 h-7 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs">📂</span>
                                                Berkas Persyaratan (Wajib Upload)
                                            </h3>
                                            <div class="bg-slate-50 rounded-xl border border-slate-100 divide-y divide-slate-100">
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-violet-100 text-violet-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                                                    <span class="text-sm text-slate-700 font-medium">KTP Pemohon</span>
                                                </div>
                                                <div class="flex items-center gap-3 px-5 py-3.5">
                                                    <span class="w-7 h-7 bg-violet-100 text-violet-700 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                                                    <span class="text-sm text-slate-700 font-medium">Proposal Acara</span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-slate-400 mt-3 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Format file yang diterima: PDF atau Gambar (JPG, PNG)
                                            </p>
                                        </div>
                                    </div>
                                </template>

                            </div>

                            <!-- Modal Footer -->
                            <div class="px-8 sm:px-10 py-5 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-bedas-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Silakan register/login untuk mengajukan permohonan
                                </p>
                                <div class="flex items-center gap-3">
                                    <button @click="closeService()"
                                        class="px-6 py-2.5 bg-white text-slate-700 font-bold rounded-xl border border-slate-200 hover:bg-slate-100 transition-all text-sm active:scale-95">
                                        Tutup
                                    </button>
                                    @auth
                                        <a href="{{ url('/dashboard') }}"
                                            class="px-6 py-2.5 bg-bedas-600 text-white font-bold rounded-xl hover:bg-bedas-700 transition-all text-sm shadow-lg shadow-bedas-200 active:scale-95">
                                            Ajukan Sekarang
                                        </a>
                                    @else
                                        <button @click="closeService(); showRegister = true;"
                                            class="px-6 py-2.5 bg-bedas-600 text-white font-bold rounded-xl hover:bg-bedas-700 transition-all text-sm shadow-lg shadow-bedas-200 active:scale-95">
                                            Daftar & Ajukan
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- News & Articles -->
    <section id="berita" class="py-24 bg-slate-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ 
            showDetail: false, 
            activeArticle: null,
            openModal(article) {
                this.activeArticle = article;
                this.showDetail = true;
                document.body.style.overflow = 'hidden';
            },
            closeModal() {
                this.showDetail = false;
                document.body.style.overflow = 'auto';
            }
        }">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6" x-data="{ shown: false }"
                x-intersect="shown = true">
                <div class="max-w-2xl">
                    <h2 class="text-4xl font-display font-extrabold text-slate-900 mb-6" x-show="shown"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        Berita & Artikel
                    </h2>
                    <p class="text-slate-500 text-lg leading-relaxed" x-show="shown"
                        x-transition:enter="transition ease-out duration-700 delay-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        Informasi terbaru seputar kegiatan warga, pembangunan infrastruktur, dan pengumuman resmi dari
                        Kecamatan Pasirjambu.
                    </p>
                </div>
                <div x-show="shown" x-transition:enter="transition ease-out duration-700 delay-400"
                    x-transition:enter-start="opacity-0 translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    <a href="{{ route('public.articles.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white text-bedas-600 font-bold rounded-xl border border-bedas-100 shadow-sm hover:bg-bedas-50 transition-all">
                        Lihat Semua Berita
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8" x-data="{ shown: false }" x-intersect="shown = true">
                @forelse($articles as $index => $article)
                    <article x-show="shown" x-transition:enter="transition ease-out duration-700"
                        style="transition-delay: {{ ($index + 1) * 200 }}ms"
                        x-transition:enter-start="opacity-0 translate-y-8"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="group bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-2xl transition-all duration-500">
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <img src="{{ $article->image_url ?? 'https://images.unsplash.com/photo-1541872703-74c5e443d1fe?auto=format&fit=crop&q=80&w=800' }}"
                                alt="{{ $article->title }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-4 left-4">
                                <span
                                    class="px-4 py-1.5 bg-bedas-600/90 backdrop-blur-md text-white text-xs font-bold rounded-full uppercase tracking-widest">{{ $article->category ?? 'Berita' }}</span>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center gap-3 text-slate-400 text-sm mb-4">
                                <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg> {{ $article->created_at->format('d M Y') }}</span>
                            </div>
                            <h3
                                class="text-xl font-bold text-slate-900 mb-4 group-hover:text-bedas-600 transition-colors line-clamp-2 leading-snug">
                                {{ $article->title }}
                            </h3>
                            <p class="text-slate-500 mb-6 line-clamp-3 leading-relaxed">
                                {{ Str::limit(strip_tags($article->content), 120) }}
                            </p>
                            <button @click="openModal({
                                                                            title: {{ json_encode($article->title) }},
                                                                            content: {{ json_encode($article->content) }},
                                                                            date: '{{ $article->created_at->format('d M Y') }}',
                                                                            category: '{{ $article->category ?? 'Berita' }}',
                                                                            image: '{{ $article->image_url ?? 'https://images.unsplash.com/photo-1541872703-74c5e443d1fe?auto=format&fit=crop&q=80&w=800' }}'
                                                                        })"
                                class="inline-flex items-center gap-2 text-bedas-600 font-bold group/link focus:outline-none">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </article>
                @empty
                    <!-- Fallback to placeholders if no articles in DB -->
                    <!-- Article 1 -->
                    <article x-show="shown" x-transition:enter="transition ease-out duration-700 delay-[200ms]"
                        x-transition:enter-start="opacity-0 translate-y-8"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="group bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-2xl transition-all duration-500">
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1541872703-74c5e443d1fe?auto=format&fit=crop&q=80&w=800"
                                alt="News Image"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-4 left-4">
                                <span
                                    class="px-4 py-1.5 bg-bedas-600/90 backdrop-blur-md text-white text-xs font-bold rounded-full uppercase tracking-widest">Pembangunan</span>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center gap-3 text-slate-400 text-sm mb-4">
                                <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg> 18 Jan 2024</span>
                                <span>•</span>
                                <span>Pasirjambu</span>
                            </div>
                            <h3
                                class="text-xl font-bold text-slate-900 mb-4 group-hover:text-bedas-600 transition-colors line-clamp-2 leading-snug">
                                Percepatan Digitalisasi Pelayanan Publik di Kecamatan Pasirjambu Melalui Sistem Terpadu
                            </h3>
                            <p class="text-slate-500 mb-6 line-clamp-3 leading-relaxed">
                                Upaya pemerintah kecamatan dalam meningkatkan kualitas layanan melalui implementasi
                                teknologi informasi untuk kemudahan masyarakat...
                            </p>
                            <button @click="openModal({
                                                                        title: 'Percepatan Digitalisasi Pelayanan Publik di Kecamatan Pasirjambu Melalui Sistem Terpadu',
                                                                        content: 'Upaya pemerintah kecamatan dalam meningkatkan kualitas layanan melalui implementasi teknologi informasi untuk kemudahan masyarakat dalam mengakses layanan publik secara cepat dan transparan. Program ini mencakup digitalisasi dokumen kependudukan dan sistem antrian online.',
                                                                        date: '18 Jan 2024',
                                                                        category: 'Pembangunan',
                                                                        image: 'https://images.unsplash.com/photo-1541872703-74c5e443d1fe?auto=format&fit=crop&q=80&w=800'
                                                                    })"
                                class="inline-flex items-center gap-2 text-bedas-600 font-bold group/link focus:outline-none">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </article>

                    <!-- Article 2 -->
                    <article x-show="shown" x-transition:enter="transition ease-out duration-700 delay-[400ms]"
                        x-transition:enter-start="opacity-0 translate-y-8"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="group bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-2xl transition-all duration-500">
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1596495573458-185682853486?auto=format&fit=crop&q=80&w=800"
                                alt="News Image"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-4 left-4">
                                <span
                                    class="px-4 py-1.5 bg-violet-600/90 backdrop-blur-md text-white text-xs font-bold rounded-full uppercase tracking-widest">Warga</span>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center gap-3 text-slate-400 text-sm mb-4">
                                <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg> 15 Jan 2024</span>
                                <span>•</span>
                                <span>Masyarakat</span>
                            </div>
                            <h3
                                class="text-xl font-bold text-slate-900 mb-4 group-hover:text-bedas-600 transition-colors line-clamp-2 leading-snug">
                                Kegiatan Gotong Royong Serentak untuk Pembersihan Lingkungan Desa Pasirjambu
                            </h3>
                            <p class="text-slate-500 mb-6 line-clamp-3 leading-relaxed">
                                Ratusan warga berpartisipasi dalam kegiatan tahunan untuk menjaga kebersihan dan kenyamanan
                                lingkungan desa secara mandiri...
                            </p>
                            <button @click="openModal({
                                                                        title: 'Kegiatan Gotong Royong Serentak untuk Pembersihan Lingkungan Desa Pasirjambu',
                                                                        content: 'Ratusan warga berpartisipasi dalam kegiatan tahunan untuk menjaga kebersihan dan kenyamanan lingkungan desa secara mandiri. Kegiatan ini meliputi pembersihan drainase, perbaikan jalan setapak, dan penanaman pohon di area publik untuk menciptakan lingkungan yang lebih asri.',
                                                                        date: '15 Jan 2024',
                                                                        category: 'Warga',
                                                                        image: 'https://images.unsplash.com/photo-1596495573458-185682853486?auto=format&fit=crop&q=80&w=800'
                                                                    })"
                                class="inline-flex items-center gap-2 text-bedas-600 font-bold group/link focus:outline-none">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </article>

                    <!-- Article 3 -->
                    <article x-show="shown" x-transition:enter="transition ease-out duration-700 delay-[600ms]"
                        x-transition:enter-start="opacity-0 translate-y-8"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="group bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-2xl transition-all duration-500">
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1590650516494-0c8e4a4dd6a5?auto=format&fit=crop&q=80&w=800"
                                alt="News Image"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-4 left-4">
                                <span
                                    class="px-4 py-1.5 bg-blue-600/90 backdrop-blur-md text-white text-xs font-bold rounded-full uppercase tracking-widest">Informasi</span>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center gap-3 text-slate-400 text-sm mb-4">
                                <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg> 12 Jan 2024</span>
                                <span>•</span>
                                <span>Kesehatan</span>
                            </div>
                            <h3
                                class="text-xl font-bold text-slate-900 mb-4 group-hover:text-bedas-600 transition-colors line-clamp-2 leading-snug">
                                Sosialisasi Program Kesehatan Masyarakat Melalui Posyandu Digital Pasirjambu
                            </h3>
                            <p class="text-slate-500 mb-6 line-clamp-3 leading-relaxed">
                                Peluncuran inisiatif baru dalam pemantauan kesehatan ibu dan anak menggunakan aplikasi
                                mobile yang terintegrasi dengan data kecamatan...
                            </p>
                            <button @click="openModal({
                                                                        title: 'Sosialisasi Program Kesehatan Masyarakat Melalui Posyandu Digital Pasirjambu',
                                                                        content: 'Peluncuran inisiatif baru dalam pemantauan kesehatan ibu dan anak menggunakan aplikasi mobile yang terintegrasi dengan data kecamatan. Masyarakat kini dapat memantau tumbuh kembang anak dan jadwal imunisasi secara real-time melalui smartphone mereka.',
                                                                        date: '12 Jan 2024',
                                                                        category: 'Kesehatan',
                                                                        image: 'https://images.unsplash.com/photo-1590650516494-0c8e4a4dd6a5?auto=format&fit=crop&q=80&w=800'
                                                                    })"
                                class="inline-flex items-center gap-2 text-bedas-600 font-bold group/link focus:outline-none">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </article>
                @endforelse
            </div>

            <!-- Detail Article Modal -->
            <div x-show="showDetail" class="fixed inset-0 z-[150] overflow-y-auto" style="display: none;" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showDetail" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" @click="closeModal()"
                        class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-md"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div x-show="showDetail" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block w-full max-w-4xl overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[32px] sm:my-8">

                        <!-- Modal Header -->
                        <div class="relative h-64 sm:h-96 overflow-hidden">
                            <img :src="activeArticle?.image" class="w-full h-full object-cover" alt="">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <button @click="closeModal()"
                                class="absolute top-6 right-6 p-3 bg-white/20 backdrop-blur-md text-white rounded-2xl hover:bg-white/40 transition-all focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="absolute bottom-8 left-8 right-8">
                                <span x-text="activeArticle?.category"
                                    class="px-4 py-1.5 bg-bedas-600 text-white text-xs font-bold rounded-full uppercase tracking-widest mb-4 inline-block"></span>
                                <h2 x-text="activeArticle?.title"
                                    class="text-3xl sm:text-4xl font-display font-bold text-white leading-tight"></h2>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-8 sm:p-12 overflow-y-auto max-h-[60vh]">
                            <div
                                class="flex items-center gap-4 text-slate-400 text-sm mb-8 border-b border-slate-100 pb-6">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-bedas-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span x-text="activeArticle?.date"></span>
                                </span>
                                <span>•</span>
                                <span class="font-bold text-slate-900">Pasirjambu Kab. Bandung</span>
                            </div>

                            <div class="prose prose-slate max-w-none">
                                <p x-text="activeArticle?.content"
                                    class="text-slate-600 text-lg leading-relaxed whitespace-pre-line"></p>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-8 sm:px-12 py-6 bg-slate-50 flex justify-end">
                            <button @click="closeModal()"
                                class="px-8 py-3 bg-white text-slate-900 font-bold rounded-xl border border-slate-200 hover:bg-slate-100 transition-all active:scale-95">
                                Tutup Berita
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative bg-[#060d19] text-slate-400 overflow-hidden font-sans">
        <!-- Animated Gradient Top Border -->
        <div class="absolute top-0 left-0 right-0 h-px">
            <div class="h-full w-full bg-gradient-to-r from-transparent via-bedas-500/60 to-transparent"></div>
        </div>

        <!-- Subtle Background Glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[200px] bg-bedas-600/[0.03] rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
            <!-- Main Footer Row -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 mb-6">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-bedas-500 to-emerald-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-bedas-900/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-lg text-white/90 tracking-tight">Pelayanan Administrasi Kecamatan Pasirjambu</span>
                </div>

                <!-- Contact Pills -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="https://maps.google.com/?q=Jl.+Batu+Reok+No.24+Pasirjambu" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.04] border border-white/[0.06] text-xs text-slate-400 hover:bg-bedas-500/10 hover:border-bedas-500/20 hover:text-bedas-400 transition-all duration-300 backdrop-blur-sm group">
                        <svg class="w-3.5 h-3.5 text-bedas-500/70 group-hover:text-bedas-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="hidden sm:inline">Jl. Batu Reok No.24, Pasirjambu</span>
                        <span class="sm:hidden">Alamat</span>
                    </a>
                    <a href="mailto:mail@kecamatanpasirjambu.bandungkab.go.id"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.04] border border-white/[0.06] text-xs text-slate-400 hover:bg-bedas-500/10 hover:border-bedas-500/20 hover:text-bedas-400 transition-all duration-300 backdrop-blur-sm group">
                        <svg class="w-3.5 h-3.5 text-bedas-500/70 group-hover:text-bedas-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">mail@kecamatanpasirjambu.bandungkab.go.id</span>
                        <span class="sm:hidden">Email</span>
                    </a>
                    <a href="tel:0225927477"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.04] border border-white/[0.06] text-xs text-slate-400 hover:bg-bedas-500/10 hover:border-bedas-500/20 hover:text-bedas-400 transition-all duration-300 backdrop-blur-sm group">
                        <svg class="w-3.5 h-3.5 text-bedas-500/70 group-hover:text-bedas-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        0225927477
                    </a>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="pt-5 border-t border-white/[0.04] flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-[11px] text-slate-500/80 tracking-wide">&copy; {{ date('Y') }} Kecamatan Pasirjambu. Maju Bersatu Padu.</p>
                <p class="text-[11px] text-slate-600/60 tracking-wide">Kabupaten Bandung, Jawa Barat 40972</p>
            </div>
        </div>
    </footer>

    <!-- Auth Modals -->
    <!-- Login Modal -->
    <div x-show="showLogin" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showLogin" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showLogin = false"
                class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="showLogin" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block px-8 py-10 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full">

                <div class="text-center mb-8">
                    <h2 class="text-2xl font-display font-bold text-slate-900">Selamat Datang Kembali</h2>
                    <p class="text-slate-500 mt-2 text-sm">Silakan masuk ke akun Anda untuk melanjutkan</p>
                </div>

                @if (session('success'))
                    <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl flex items-start gap-4 shadow-md">
                        <div class="bg-emerald-100 p-2.5 rounded-xl text-emerald-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-emerald-800 uppercase tracking-widest">Berhasil!</h4>
                            <p class="text-[11px] text-emerald-600 mt-1 font-bold leading-tight">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                @endif
                @if ($errors->has('email') || $errors->has('password'))
                    <div
                        class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl flex items-start gap-4 shadow-md animate-shake">
                        <div class="bg-red-100 p-2.5 rounded-xl text-red-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-red-800 uppercase tracking-widest">Akses Ditolak</h4>
                            <p class="text-[11px] text-red-600 mt-1 font-bold leading-tight">
                                Email atau kata sandi yang Anda masukkan tidak valid. Silakan periksa kembali.
                            </p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500 transition-all outline-none"
                            placeholder="nama@email.com">
                        @if ($errors->has('email'))
                            <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <div>
                        <div class="flex justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Kata Sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-xs font-semibold text-bedas-600 hover:text-bedas-700">Lupa sandi?</a>
                            @endif
                        </div>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500 transition-all outline-none"
                            placeholder="••••••••">
                        @if ($errors->has('password'))
                            <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $errors->first('password') }}</p>
                        @endif
                    </div>

                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="w-4 h-4 text-bedas-600 border-slate-300 rounded focus:ring-bedas-500">
                        <label for="remember_me" class="ml-2 text-sm text-slate-600">Ingat saya untuk login
                            berikutnya</label>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-bedas-600 text-white font-bold rounded-xl shadow-lg shadow-bedas-200 hover:bg-bedas-700 transition-all transform active:scale-[0.98]">
                        Masuk Sekarang
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-500">
                        Belum punya akun?
                        <button @click="toggleAuth()" class="font-bold text-bedas-600 hover:text-bedas-700">Daftar Akun
                            Baru</button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div x-show="showRegister" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showRegister" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showRegister = false"
                class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="showRegister" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block px-8 py-10 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full">

                <div class="text-center mb-6">
                    <h2 class="text-2xl font-display font-bold text-slate-900">Daftar Akun Baru</h2>
                    <p class="text-slate-500 mt-1 text-sm">Lengkapi data Anda untuk mulai menggunakan layanan</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{
                    regName: '{{ old('name') }}',
                    regEmail: '{{ old('email') }}',
                    regPhone: '{{ old('phone') }}',
                    regPassword: '',
                    regPasswordConfirm: '',
                    
                    isEmailValid(email) {
                        if (!email) return null;
                        return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email);
                    },
                    isPhoneValid(phone) {
                        if (!phone) return null;
                        return /^(08|628)[0-9]{8,13}$/.test(phone);
                    },
                    isNameValid(name) {
                        if (!name) return null;
                        return /^[a-zA-Z\s\.\',]+$/.test(name);
                    }
                }">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap <span class="text-red-500">*</span></label>
                            <span class="text-[11px] text-slate-400">Sesuai KTP</span>
                        </div>
                        <input type="text" name="name" x-model="regName" required
                            class="w-full px-4 py-2.5 rounded-xl border text-sm transition-all outline-none"
                            :class="regName.length > 0 ? (isNameValid(regName) ? 'border-emerald-400 focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500' : 'border-red-300 bg-red-50/30 focus:ring-4 focus:ring-red-100 focus:border-red-500') : 'border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500'"
                            placeholder="Contoh: Ahmad Hidayat">
                        <template x-if="regName && !isNameValid(regName)">
                            <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1 font-medium">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Nama hanya boleh huruf, spasi, titik, dan koma.
                            </p>
                        </template>
                        @if ($errors->has('name'))
                            <p class="mt-1 text-[11px] text-red-500 font-medium">{{ $errors->first('name') }}</p>
                        @endif
                    </div>

                    <!-- Alamat Email -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email <span class="text-red-500">*</span></label>
                            <span class="text-[11px] text-slate-400">Format: nama@domain.com</span>
                        </div>
                        <input type="email" name="email" x-model="regEmail" required
                            class="w-full px-4 py-2.5 rounded-xl border text-sm transition-all outline-none"
                            :class="regEmail.length > 0 ? (isEmailValid(regEmail) ? 'border-emerald-400 focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500' : 'border-amber-400 bg-amber-50/30 focus:ring-4 focus:ring-amber-100 focus:border-amber-500') : 'border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500'"
                            placeholder="nama@email.com">
                        
                        <!-- Real-time Email Format Feedback -->
                        <div class="mt-1">
                            <template x-if="regEmail.length > 0 && isEmailValid(regEmail)">
                                <p class="text-[11px] text-emerald-600 flex items-center gap-1 font-medium">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    Format email valid
                                </p>
                            </template>
                            <template x-if="regEmail.length > 0 && !isEmailValid(regEmail)">
                                <p class="text-[11px] text-amber-600 flex items-center gap-1 font-medium">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Format email harus lengkap dengan simbol @ dan domain (contoh: budi@gmail.com)
                                </p>
                            </template>
                            <template x-if="!regEmail || regEmail.length === 0">
                                <p class="text-[11px] text-slate-400">Gunakan email aktif (Gmail, Yahoo, Outlook, dll) untuk masuk ke akun</p>
                            </template>
                        </div>
                        @if ($errors->has('email') && !$errors->has('password'))
                            <p class="mt-1 text-[11px] text-red-500 font-medium">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <!-- Nomor WhatsApp -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nomor WhatsApp <span class="text-red-500">*</span></label>
                            <span class="text-[11px] text-slate-400">Awalan 08 / 628</span>
                        </div>
                        <input type="tel" name="phone" x-model="regPhone" required
                            class="w-full px-4 py-2.5 rounded-xl border text-sm transition-all outline-none"
                            :class="regPhone.length > 0 ? (isPhoneValid(regPhone) ? 'border-emerald-400 focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500' : 'border-amber-400 bg-amber-50/30 focus:ring-4 focus:ring-amber-100 focus:border-amber-500') : 'border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500'"
                            placeholder="Contoh: 081234567890">
                        <div class="mt-1">
                            <template x-if="regPhone.length > 0 && !isPhoneValid(regPhone)">
                                <p class="text-[11px] text-amber-600 flex items-center gap-1 font-medium">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Nomor harus 10-15 digit dan diawali 08 atau 628
                                </p>
                            </template>
                            <template x-if="!regPhone || regPhone.length === 0">
                                <p class="text-[11px] text-slate-400">Untuk menerima notifikasi status permohonan surat</p>
                            </template>
                        </div>
                        @if ($errors->has('phone'))
                            <p class="mt-1 text-[11px] text-red-500 font-medium">{{ $errors->first('phone') }}</p>
                        @endif
                    </div>

                    <!-- Kata Sandi -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi <span class="text-red-500">*</span></label>
                            <span class="text-[11px]" :class="regPassword.length >= 8 ? 'text-emerald-600 font-semibold' : 'text-slate-400'">Min. 8 karakter</span>
                        </div>
                        <input type="password" name="password" x-model="regPassword" required
                            class="w-full px-4 py-2.5 rounded-xl border text-sm transition-all outline-none"
                            :class="regPassword.length > 0 ? (regPassword.length >= 8 ? 'border-emerald-400 focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500' : 'border-amber-400 bg-amber-50/30 focus:ring-4 focus:ring-amber-100 focus:border-amber-500') : 'border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500'"
                            placeholder="Minimal 8 karakter">
                        <template x-if="regPassword.length > 0 && regPassword.length < 8">
                            <p class="mt-1 text-[11px] text-amber-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span x-text="'Kurang ' + (8 - regPassword.length) + ' karakter lagi'"></span>
                            </p>
                        </template>
                        @if ($errors->has('password'))
                            <p class="mt-1 text-[11px] text-red-500 font-medium">{{ $errors->first('password') }}</p>
                        @endif
                    </div>

                    <!-- Konfirmasi Sandi -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Konfirmasi Sandi <span class="text-red-500">*</span></label>
                            <span class="text-[11px] text-slate-400">Ketik ulang kata sandi</span>
                        </div>
                        <input type="password" name="password_confirmation" x-model="regPasswordConfirm" required
                            class="w-full px-4 py-2.5 rounded-xl border text-sm transition-all outline-none"
                            :class="regPasswordConfirm.length > 0 ? (regPasswordConfirm === regPassword ? 'border-emerald-400 focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500' : 'border-red-300 bg-red-50/30 focus:ring-4 focus:ring-red-100 focus:border-red-500') : 'border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500'"
                            placeholder="Ulangi kata sandi">
                        <template x-if="regPasswordConfirm.length > 0 && regPasswordConfirm !== regPassword">
                            <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1 font-medium">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Konfirmasi kata sandi tidak cocok
                            </p>
                        </template>
                        <template x-if="regPasswordConfirm.length > 0 && regPasswordConfirm === regPassword">
                            <p class="mt-1 text-[11px] text-emerald-600 flex items-center gap-1 font-medium">
                                <svg class="w-3.5 h-3.5 flex-shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Kata sandi cocok
                            </p>
                        </template>
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 bg-bedas-600 text-white font-bold rounded-xl shadow-lg shadow-bedas-200 hover:bg-bedas-700 transition-all transform active:scale-[0.98] mt-2">
                        Daftar Sekarang
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-500">
                        Sudah punya akun?
                        <button @click="toggleAuth()" class="font-bold text-bedas-600 hover:text-bedas-700">Masuk
                            Disini</button>
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>