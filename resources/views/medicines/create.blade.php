<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-subtitle">{{ __('Tambah Obat Baru') }}</p>
            <h1 class="page-title">{{ __('Input Data Obat') }}</h1>
        </div>
        <x-button :href="route('medicines.index')" variant="ghost">
            {{ __('Kembali') }}
        </x-button>
    </x-slot>

    <section class="card">
        <form method="POST" action="{{ route('medicines.store') }}">
            @csrf
            <div class="card-body space-y-6">
                <div class="form-grid">
                    <x-form.input
                        id="kode"
                        name="kode"
                        label="{{ __('Kode Obat') }}"
                        placeholder="OBT001"
                        required />
                    <x-form.input
                        id="nama"
                        name="nama"
                        label="{{ __('Nama Obat') }}"
                        required />
                    <x-form.input
                        id="satuan"
                        name="satuan"
                        label="{{ __('Satuan') }}"
                        :value="old('satuan', 'tablet')"
                        required />
                    <x-form.input
                        id="stok"
                        name="stok"
                        type="number"
                        label="{{ __('Stok Awal') }}"
                        :value="old('stok', 0)"
                        required />
                    <x-form.input
                        id="stok_minimal"
                        name="stok_minimal"
                        type="number"
                        label="{{ __('Stok Minimal') }}"
                        :value="old('stok_minimal', 0)" />
                    <x-form.input
                        id="keterangan"
                        name="keterangan"
                        type="textarea"
                        rows="3"
                        label="{{ __('Keterangan') }}" />
                </div>
            </div>
            <div class="card-footer flex justify-end gap-3">
                <x-button :href="route('medicines.index')" type="button" variant="ghost">
                    {{ __('Batal') }}
                </x-button>
                <x-button type="submit" icon="check-circle">
                    {{ __('Simpan Obat') }}
                </x-button>
            </div>
        </form>
    </section>
</x-app-layout>
