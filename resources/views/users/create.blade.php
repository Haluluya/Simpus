<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Tambah User Baru
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-2xl">
                <form method="POST" action="{{ route('users.store') }}" class="p-6 sm:p-8">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="mb-6">
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required 
                               class="block w-full h-10 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 text-sm"
                               placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIP -->
                    <div class="mb-6">
                        <label for="nik" class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">
                            NIP <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nik" 
                               id="nik" 
                               value="{{ old('nik') }}"
                               required 
                               class="block w-full h-10 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 text-sm"
                               placeholder="Masukkan NIP">
                        @error('nik')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               required 
                               class="block w-full h-10 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 text-sm"
                               placeholder="nama@puskesmas.go.id">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="mb-6">
                        <label for="roles" class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="roles[]" 
                                id="roles" 
                                required 
                                class="block w-full h-10 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 text-sm">
                            <option value="">Pilih Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ in_array($role->name, old('roles', [])) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('roles')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('roles.0')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               class="block w-full h-10 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 text-sm"
                               placeholder="Masukkan password">
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required 
                               class="block w-full h-10 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 text-sm"
                               placeholder="Ulangi password">
                    </div>

                    <!-- Status User -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between rounded-xl border border-gray-200 p-4">
                            <div>
                                <label for="is_active" class="text-sm font-medium text-gray-700">
                                    Status User
                                </label>
                                <p class="text-xs text-gray-500">Aktifkan akun setelah dibuat</p>
                            </div>
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1"
                                       {{ old('is_active', 1) ? 'checked' : '' }}
                                       class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('users.index') }}" 
                           class="rounded-xl border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Batal
                        </a>
                        <button type="submit" 
                                class="rounded-xl bg-[#2563EB] px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>