<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-subtitle">{{ __('Perbarui Stok & Data Obat') }}</p>
            <h1 class="page-title">{{ $medicine->nama }}</h1>
        </div>
        <x-button :href="route('medicines.index')" variant="ghost">
            {{ __('Kembali') }}
        </x-button>
    </x-slot>

    <section class="card">
        <form method="POST" action="{{ route('medicines.update', $medicine) }}">
            @csrf
            @method('PUT')
            <div class="card-body space-y-6">
                <div class="form-grid">
                    <x-form.input
                        id="kode"
                        name="kode"
                        label="{{ __('Kode Obat') }}"
                        :value="old('kode', $medicine->kode)"
                        required />
                    <x-form.input
                        id="nama"
                        name="nama"
                        label="{{ __('Nama Obat') }}"
                        :value="old('nama', $medicine->nama)"
                        required />
                    <x-form.input
                        id="satuan"
                        name="satuan"
                        label="{{ __('Satuan') }}"
                        :value="old('satuan', $medicine->satuan)"
                        required />
                    <x-form.input
                        id="stok"
                        name="stok"
                        type="number"
                        label="{{ __('Stok Saat Ini') }}"
                        :value="old('stok', $medicine->stok)"
                        required />
                    <x-form.input
                        id="stok_minimal"
                        name="stok_minimal"
                        type="number"
                        label="{{ __('Stok Minimal') }}"
                        :value="old('stok_minimal', $medicine->stok_minimal)" />
                    <x-form.input
                        id="keterangan"
                        name="keterangan"
                        type="textarea"
                        rows="3"
                        label="{{ __('Keterangan') }}"
                        :value="old('keterangan', $medicine->keterangan)" />
                </div>
            </div>
            <div class="card-footer flex justify-end gap-3">
                <x-button :href="route('medicines.index')" type="button" variant="ghost">
                    {{ __('Batal') }}
                </x-button>
                <x-button type="submit" icon="check-circle">
                    {{ __('Simpan Perubahan') }}
                </x-button>
            </div>
        </form>
    </section>
</x-app-layout>
