<x-guest-layout>
    <div class="mb-6 text-sm text-slate-500 leading-relaxed">
        Lupa kata sandi Anda? Tidak masalah. Cukup masukkan alamat email yang terdaftar dan kami akan mengirimkan tautan
        untuk mengatur ulang kata sandi Anda.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full justify-center">
                Kirim Tautan Reset Kata Sandi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>