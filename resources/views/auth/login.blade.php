<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">Masuk ke Akun Anda</h2>
        <p class="text-slate-500 text-sm">Silakan masukkan email dan kata sandi</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl flex items-start gap-4 shadow-md">
            <div class="bg-emerald-100 p-2.5 rounded-xl text-emerald-600 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-emerald-800 uppercase tracking-widest">Pendaftaran Berhasil!</h4>
                <p class="text-[11px] text-emerald-600 mt-1 font-bold leading-tight">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif

    @if ($errors->any())
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
                    Email atau kata sandi yang Anda masukkan tidak valid. Silakan periksa kembali dan coba lagi.
                </p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Kata Sandi" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-slate-300 text-bedas-600 shadow-sm focus:ring-bedas-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">Ingat Saya</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-xs text-slate-500 hover:text-bedas-600 transition-colors"
                    href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <div class="mt-4">
            <x-primary-button class="w-full justify-center">
                Masuk
            </x-primary-button>
        </div>

        <div class="mt-6 text-center text-sm text-slate-500">
            Belum punya akun? <a href="{{ route('register') }}"
                class="font-semibold text-bedas-600 hover:text-bedas-500">Daftar sekarang</a>
        </div>
    </form>
</x-guest-layout>