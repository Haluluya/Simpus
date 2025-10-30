<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-subtitle">Pengaturan Sistem</p>
            <h1 class="page-title">Atur Peran Pengguna</h1>
        </div>
        <x-button :href="route('users.index')" variant="outline" icon="chevron-down">
            Kembali ke daftar
        </x-button>
    </x-slot>

    <section class="max-w-4xl space-y-6">
        <article class="card">
            <div class="card-body">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $user->name }}</h2>
                        <p class="text-sm text-slate-500">{{ $user->email }}</p>
                    </div>
                    <div class="space-y-1 text-right text-sm text-slate-500">
                        <p>Peran aktif:</p>
                        <div class="flex flex-wrap justify-end gap-2">
                            @forelse ($user->roles as $role)
                                <span class="badge badge-info">{{ \Illuminate\Support\Str::headline($role->name) }}</span>
                            @empty
                                <span class="badge badge-warning">Belum diatur</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <article class="card">
            <form method="POST" action="{{ route('users.update', $user) }}" class="card-body space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="section-title">Pilih Peran</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Centang peran yang sesuai dengan tanggung jawab pengguna. Perubahan berlaku setelah disimpan.
                    </p>
                </div>

                @php
                    $assignedRoles = old('roles', $user->roles->pluck('name')->all());
                @endphp

                <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($roles as $roleName)
                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 hover:border-brand/40 hover:bg-brand/5">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $roleName }}"
                                   class="rounded border-slate-300 text-brand focus:ring-brand/40"
                                   @checked($assignedRoles && in_array($roleName, $assignedRoles, true))>
                            <span class="text-sm font-medium text-slate-700">{{ \Illuminate\Support\Str::headline($roleName) }}</span>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('roles')" />

                <div class="flex justify-end gap-3">
                    <x-button :href="route('users.index')" variant="outline">Batal</x-button>
                    <x-button type="submit" icon="check-circle">Simpan Perubahan</x-button>
                </div>
            </form>
        </article>
    </section>
</x-app-layout>
