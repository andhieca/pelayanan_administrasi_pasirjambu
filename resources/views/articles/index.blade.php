<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berita & Artikel - Kecamatan Pasirjambu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bedas: {
                            50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac',
                            400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d',
                            800: '#166534', 900: '#14532d', 950: '#052c16',
                        },
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body class="bg-white text-slate-900 font-sans selection:bg-bedas-100 selection:text-bedas-900">

    <!-- Navigation (Simplified from Welcome) -->
    <nav class="fixed top-0 left-0 right-0 z-[100] glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('logo-kab-bandung.png') }}" alt="Logo"
                        class="h-10 w-auto group-hover:scale-110 transition-transform duration-300">
                    <div class="flex flex-col">
                        <span
                            class="text-lg font-display font-extrabold text-slate-900 tracking-tight leading-none">PASIRJAMBU</span>
                        <span
                            class="text-[10px] font-bold text-bedas-600 tracking-[0.2em] uppercase leading-none mt-1">Kab.
                            Bandung</span>
                    </div>
                </a>
                <div class="hidden md:flex items-center gap-8 ml-10">
                    <a href="{{ url('/#layanan') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-bedas-600 transition-colors">Layanan</a>
                    <a href="{{ url('/#berita') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-bedas-600 transition-colors">Berita</a>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-4">
                        <a href="{{ url('/') }}"
                            class="text-sm font-bold text-slate-500 hover:text-bedas-600 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Beranda
                        </a>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-slate-600 hover:text-bedas-600 transition-colors focus:outline-none">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="md:hidden bg-white border-t border-slate-100 py-4 px-4 shadow-xl"
             style="display: none;">
            <div class="flex flex-col gap-4">
                <a href="{{ url('/') }}" class="text-base font-bold text-slate-900 px-2 py-1">Beranda</a>
                <a href="{{ url('/#layanan') }}" @click="mobileMenuOpen = false" class="text-base font-semibold text-slate-600 hover:text-bedas-600 px-2 py-1">Layanan</a>
                <a href="{{ url('/#berita') }}" @click="mobileMenuOpen = false" class="text-base font-semibold text-slate-600 hover:text-bedas-600 px-2 py-1">Berita</a>
            </div>
        </div>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ 
            showDetail: false, 
            activeArticle: null,
            mobileMenuOpen: false,
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
            <!-- Header Section -->
            <div class="mb-16 text-center max-w-3xl mx-auto">
                <h1 class="text-5xl font-display font-extrabold text-slate-900 mb-6 leading-tight">
                    Arsip <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-bedas-600 to-emerald-500">Berita &
                        Artikel</span> Desa
                </h1>
                <p class="text-slate-500 text-lg">
                    Telusuri lebih banyak informasi, kegiatan, kependudukan, dan pengumuman resmi dari Kecamatan
                    Pasirjambu.
                </p>
            </div>

            <!-- Articles Grid -->
            <div class="grid md:grid-cols-3 gap-8 mb-16" x-data="{ shown: false }" x-intersect="shown = true">
                @forelse($articles as $index => $article)
                    <article x-show="shown" x-transition:enter="transition ease-out duration-700"
                        style="transition-delay: {{ ($index % 3 + 1) * 150 }}ms"
                        x-transition:enter-start="opacity-0 translate-y-8"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="group bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <img src="{{ $article->image ? (filter_var($article->image, FILTER_VALIDATE_URL) ? $article->image : asset('storage/' . $article->image)) : 'https://images.unsplash.com/photo-1541872703-74c5e443d1fe?auto=format&fit=crop&q=80&w=800' }}"
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
                            <p class="text-slate-500 mb-6 line-clamp-3 leading-relaxed text-sm">
                                {{ Str::limit(strip_tags($article->content), 120) }}
                            </p>
                            <button @click="openModal({
                                                    title: {{ json_encode($article->title) }},
                                                    content: {{ json_encode($article->content) }},
                                                    date: '{{ $article->created_at->format('d M Y') }}',
                                                    category: '{{ $article->category ?? 'Berita' }}',
                                                    image: '{{ $article->image ? (filter_var($article->image, FILTER_VALIDATE_URL) ? $article->image : asset('storage/' . $article->image)) : 'https://images.unsplash.com/photo-1541872703-74c5e443d1fe?auto=format&fit=crop&q=80&w=800' }}'
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
                    <div class="col-span-full py-20 text-center">
                        <div class="bg-slate-50 rounded-[40px] p-12 inline-block mb-6">
                            <svg class="w-16 h-16 text-slate-300 mx-auto" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 2v6h6"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">Belum Ada Artikel</h3>
                        <p class="text-slate-500 mt-2">Maaf, saat ini belum ada artikel yang bisa ditampilkan.</p>
                        <a href="{{ url('/') }}"
                            class="mt-8 inline-block px-8 py-3 bg-bedas-600 text-white font-bold rounded-xl hover:bg-bedas-700 transition-all">Kembali
                            ke Beranda</a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $articles->links() }}
            </div>

            <!-- Detail Modal (Same as welcome) -->
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
                        class="inline-block w-full max-w-4xl overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[32px] sm:my-8 text-slate-900">

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
    </main>

    <footer class="bg-slate-50 border-t border-slate-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-400 text-sm">
            <p>© {{ date('Y') }} Kecamatan Pasirjambu. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>