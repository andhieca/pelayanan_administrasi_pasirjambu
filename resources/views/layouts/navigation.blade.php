<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur-md border-b border-gray-100 fixed w-full top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="px-3 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-2">
            <!-- Left Side: Hamburger & Brand -->
            <div class="flex items-center min-w-0 flex-shrink-0">
                <!-- Hamburger (Mobile) -->
                <div class="flex items-center sm:hidden mr-1.5 flex-shrink-0">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-2.5 group transition-all duration-300">
                        <div class="relative flex-shrink-0">
                            <div
                                class="absolute -inset-1 bg-gradient-to-tr from-bedas-500 to-emerald-500 rounded-lg blur opacity-25 group-hover:opacity-40 transition duration-300">
                            </div>
                            <img src="{{ asset('logo-kab-bandung.png') }}" alt="Logo"
                                class="relative w-8 sm:w-10 h-auto drop-shadow-sm">
                        </div>
                        <div class="flex flex-col leading-tight hidden sm:flex">
                            <span class="text-[10px] font-bold text-bedas-600 uppercase tracking-widest">Pelayanan
                                Administrasi</span>
                            <span class="text-lg font-black text-slate-800 tracking-tighter">PASIRJAMBU</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Right Side: Settings Dropdown & Notifications -->
            <div class="flex items-center gap-1.5 sm:gap-3 flex-shrink-0">
                @php
                    $userNotifications = Auth::check() 
                        ? Auth::user()->permohonans()
                            ->whereIn('status', ['selesai', 'ditolak'])
                            ->orderBy('updated_at', 'desc')
                            ->take(10)
                            ->get() 
                        : collect();
                    $unreadCount = $userNotifications->count();
                @endphp

                <!-- Notification Bell Dropdown -->
                <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
                    <button @click="notifOpen = !notifOpen"
                        class="relative p-2 sm:p-2.5 rounded-xl text-slate-600 bg-slate-50 hover:bg-slate-100 hover:text-bedas-600 focus:outline-none transition-all duration-200"
                        title="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>

                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-white text-[9px] font-black items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Panel (Responsive: fixed on mobile, absolute on desktop) -->
                    <div x-show="notifOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                        class="fixed sm:absolute inset-x-3 sm:inset-x-auto sm:right-0 top-[68px] sm:top-auto sm:mt-2 w-auto sm:w-96 max-h-[80vh] bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50 divide-y divide-slate-100"
                        style="display: none;">
                        
                        <!-- Header -->
                        <div class="p-3.5 sm:p-4 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                                </svg>
                                <span class="font-bold text-sm">Notifikasi Permohonan</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white">
                                {{ $unreadCount }} Pemberitahuan
                            </span>
                        </div>

                        <!-- Notification List -->
                        <div class="max-h-[320px] overflow-y-auto divide-y divide-slate-100">
                            @forelse($userNotifications as $item)
                                <div class="p-3.5 sm:p-4 hover:bg-slate-50 transition-colors relative group">
                                    @if($item->status === 'selesai')
                                        <!-- Status Selesai -->
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-1 mb-1">
                                                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Permohonan Selesai</span>
                                                    <span class="text-[10px] text-slate-400">{{ $item->updated_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-xs font-semibold text-slate-800 truncate">{{ $item->jenis_layanan }}</p>
                                                @if($item->nomor_surat)
                                                    <p class="text-[11px] text-slate-500 mt-0.5">No: <span class="font-mono text-slate-700 font-semibold">{{ $item->nomor_surat }}</span></p>
                                                @endif
                                                
                                                <!-- WhatsApp forwarded indicator -->
                                                <div class="mt-2 flex items-center justify-between gap-2">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                                        <svg class="w-3 h-3 flex-shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                        Diteruskan ke WA
                                                    </span>
                                                    <a href="{{ route('masyarakat.print', $item->id) }}" target="_blank"
                                                        class="text-[11px] font-bold text-bedas-600 hover:text-bedas-800 underline">
                                                        Cetak Surat &rarr;
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($item->status === 'ditolak')
                                        <!-- Status Ditolak / Perlu Perbaikan -->
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-1 mb-1">
                                                    <span class="text-xs font-bold text-red-600 uppercase tracking-wider">Perlu Perbaikan</span>
                                                    <span class="text-[10px] text-slate-400">{{ $item->updated_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-xs font-semibold text-slate-800 truncate">{{ $item->jenis_layanan }}</p>
                                                <p class="text-[11px] text-red-600 mt-0.5 line-clamp-2">{{ $item->keterangan ?: 'Terdapat berkas/data yang perlu diperbaiki.' }}</p>
                                                
                                                <!-- WhatsApp forwarded indicator -->
                                                <div class="mt-2 flex items-center justify-between gap-2">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                                        <svg class="w-3 h-3 flex-shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                        Rincian ke WA
                                                    </span>
                                                    <a href="{{ route('dashboard') }}"
                                                        class="text-[11px] font-bold text-red-600 hover:text-red-800 underline">
                                                        Perbaiki &rarr;
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700">Belum Ada Notifikasi</p>
                                    <p class="text-[11px] text-slate-400 mt-1 max-w-[200px] mx-auto">Status permohonan selesai atau perbaikan akan muncul di sini dan diteruskan ke WhatsApp Anda.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Footer -->
                        <div class="p-3 bg-slate-50 text-center">
                            <a href="{{ route('dashboard') }}" class="text-xs font-bold text-bedas-600 hover:text-bedas-700">
                                Lihat Semua Riwayat Permohonan &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 border border-transparent text-xs sm:text-sm leading-4 font-bold rounded-xl text-slate-700 bg-slate-50 hover:bg-slate-100 focus:outline-none transition ease-in-out duration-150 max-w-[125px] sm:max-w-[200px]">
                            <div class="truncate mr-1 sm:mr-2 text-xs sm:text-sm">{{ Auth::user()->name }}</div>
                            <span
                                class="hidden md:inline-block px-2 py-0.5 bg-bedas-100 text-bedas-700 text-[10px] rounded-full uppercase mr-1.5 flex-shrink-0">{{ Auth::user()->role }}</span>

                            <svg class="fill-current h-3.5 w-3.5 sm:h-4 sm:w-4 flex-shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile Sidebar Overlay/Menu) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(auth()->user()->role === 'petugas')
                <x-responsive-nav-link :href="route('petugas.articles.index')"
                    :active="request()->routeIs('petugas.articles.*')">
                    {{ __('Kelola Berita') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('petugas.users.index')"
                    :active="request()->routeIs('petugas.users.*')">
                    {{ __('Kelola Pengguna') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-bold text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>