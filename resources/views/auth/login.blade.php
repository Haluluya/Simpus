<x-guest-layout>
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="space-y-2 text-center">
        <h2 class="text-2xl font-semibold text-[#0F172A]">Masuk ke SIMPUS</h2>
        <p class="text-sm text-[#6B7280]">Silakan masukkan email dan kata sandi akun petugas Anda</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div class="space-y-2 text-left">
            <label for="email" class="text-sm font-semibold text-[#0F172A]">Email</label>
            <x-text-input
                id="email"
                class="input"
                type="email"
                name="email"
                :value="old('email')"
                placeholder="admin@simpus.test"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-[#DC2626]" />
        </div>

        <div class="space-y-2 text-left">
            <label for="password" class="text-sm font-semibold text-[#0F172A]">Kata Sandi</label>
            <x-text-input
                id="password"
                class="input"
                type="password"
                name="password"
                placeholder="Masukkan kata sandi"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-[#DC2626]" />
        </div>

        <div class="flex items-center justify-between text-sm">
            <label for="remember_me" class="inline-flex items-center gap-2 text-[#4B5563]">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-[#CBD5F5] text-[#2563EB] focus:ring-[#2563EB]" name="remember">
                <span>{{ __('Ingat saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="font-semibold text-[#2563EB] hover:text-[#1D4ED8]" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-full">
            {{ __('Masuk') }}
        </button>
    </form>

    <div class="mt-8 rounded-[16px] border border-[#E2E8F0] bg-[#F8FAFF] px-5 py-4 text-xs text-[#4B5563]">
        <p class="font-semibold text-[#2563EB]">Akun Percobaan</p>
        <ul class="mt-2 space-y-1">
            <li>Admin: <span class="font-semibold">admin@simpus.test / password123</span></li>
            <li>Dokter: dokter@simpus.test / password123</li>
            <li>Pendaftaran: pendaftaran@simpus.test / password123</li>
        </ul>
    </div>
</x-guest-layout>
