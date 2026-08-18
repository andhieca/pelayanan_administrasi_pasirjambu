<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">Daftar Akun Baru</h2>
        <p class="text-slate-500 text-sm">Lengkapi data diri Anda untuk mendaftar</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="name" value="Nama Lengkap *" />
                <span class="text-xs text-slate-400">Sesuai KTP</span>
            </div>
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" placeholder="Contoh: Ahmad Hidayat" />
            <p class="mt-1 text-xs text-slate-500">Hanya huruf, spasi, titik, dan koma.</p>
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <div class="flex items-center justify-between">
                <x-input-label for="email" value="Alamat Email *" />
                <span class="text-xs text-slate-400">Format: nama@domain.com</span>
            </div>
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autocomplete="username" placeholder="nama@email.com" />
            <p class="mt-1 text-xs text-slate-500">Gunakan email aktif yang valid (contoh: user@gmail.com, budi@yahoo.com)</p>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Phone / WhatsApp -->
        <div class="mt-4">
            <div class="flex items-center justify-between">
                <x-input-label for="phone" value="Nomor WhatsApp *" />
                <span class="text-xs text-slate-400">Awalan 08 / 628</span>
            </div>
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </span>
                <x-text-input id="phone" class="block w-full pl-10" type="tel" name="phone" :value="old('phone')" required
                    placeholder="Contoh: 081234567890" autocomplete="tel" />
            </div>
            <p class="mt-1 text-xs text-slate-500">Nomor 10-15 digit untuk notifikasi status permohonan surat via WhatsApp</p>
            <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Kata Sandi *" />
                <span class="text-xs text-slate-400">Minimal 8 karakter</span>
            </div>

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" placeholder="Minimal 8 karakter" />

            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <div class="flex items-center justify-between">
                <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi *" />
                <span class="text-xs text-slate-400">Ulangi sandi yang sama</span>
            </div>

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                Daftar Sekarang
            </x-primary-button>
        </div>

        <div class="mt-6 text-center text-sm text-slate-500">
            Sudah punya akun? <a href="{{ route('login') }}"
                class="font-semibold text-bedas-600 hover:text-bedas-500">Masuk disini</a>
        </div>
    </form>
</x-guest-layout>