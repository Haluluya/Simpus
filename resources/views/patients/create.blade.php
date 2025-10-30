<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-slate-700">
                Pendaftaran Pasien Baru
            </h2>
            <a href="{{ route('patients.index') }}" class="inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring focus:ring-slate-200 focus:ring-offset-2">
                Kembali ke daftar
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl border border-white/60 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('patients.store') }}" class="space-y-8">
            @csrf
            @include('patients.partials.form')

            <div class="flex justify-end gap-3">
                <a href="{{ route('patients.index') }}" class="inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring focus:ring-slate-200 focus:ring-offset-2">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring focus:ring-emerald-400 focus:ring-offset-2">
                    Simpan Data Pasien
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
