<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="page-subtitle">{{ __('Inventaris Obat') }}</p>
                <h1 class="page-title">{{ __('Kelola Obat') }}</h1>
            </div>
            @can('medicine.create')
                <x-button :href="route('medicines.create')" icon="plus">
                    {{ __('Obat Baru') }}
                </x-button>
            @endcan
        </div>
    </x-slot>

    <section class="card">
        <div class="card-header flex-col gap-4">
            <div>
                <h2 class="section-title">{{ __('Filter Daftar Obat') }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ __('Cari berdasarkan nama atau kode obat.') }}</p>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="grid gap-4 md:grid-cols-2 lg:grid-cols-6">
                <div class="lg:col-span-3">
                    <label for="search" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        {{ __('CARI') }}
                    </label>
                    <input
                        id="search"
                        name="search"
                        type="search"
                        value="{{ $search }}"
                        placeholder="{{ __('Nama atau kode obat') }}"
                        data-autocomplete="medicines"
                        data-autocomplete-url="{{ route('search.suggestions') }}"
                        data-autocomplete-submit="true"
                        class="input mt-1"
                    >
                </div>
                <div class="flex items-end gap-2">
                    <x-button type="submit" icon="magnifying-glass" class="flex-1">
                        {{ __('Terapkan') }}
                    </x-button>
                    <x-button :href="route('medicines.index')" variant="outline" icon="arrow-path">
                        <span class="hidden sm:inline">{{ __('Atur Ulang') }}</span>
                    </x-button>
                </div>
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <h2 class="section-title">{{ __('Data Obat') }}</h2>
        </div>
        <div class="card-body">
            @if ($medicines->isEmpty())
                <div class="py-12 text-center">
                    <p class="text-slate-500">{{ __('Tidak ada data obat') }}</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">KODE</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">NAMA OBAT</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">SATUAN</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">STOK</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">MINIMAL</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">KETERANGAN</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-600">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($medicines as $medicine)
                                <tr @class([
                                    'border-b border-slate-100 transition-colors hover:bg-slate-50',
                                    'bg-amber-50' => $medicine->stok <= $medicine->stok_minimal
                                ])>
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $medicine->kode }}</td>
                                    <td class="px-4 py-3 text-slate-900">{{ $medicine->nama }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $medicine->satuan }}</td>
                                    <td class="px-4 py-3 font-semibold @if ($medicine->stok <= $medicine->stok_minimal) text-amber-600 @else text-slate-900 @endif">
                                        {{ $medicine->stok }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">{{ $medicine->stok_minimal }}</td>
                                    <td class="px-4 py-3 text-xs text-slate-500">{{ $medicine->keterangan ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('medicines.edit', $medicine) }}"
                                                class="text-blue-600 hover:text-blue-800 transition-colors text-sm font-medium">
                                                {{ __('Ubah') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $medicines->links() }}
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
