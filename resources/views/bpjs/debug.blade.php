<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] py-6">
        <div class="mx-auto max-w-4xl px-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-[#0F172A]">Perbaikan Status BPJS</h1>
                <p class="mt-1 text-sm text-[#64748B]">Debug dan perbaikan status BPJS secara manual</p>
            </div>

            @if(session('status'))
                <div class="mb-4 rounded-lg bg-emerald-50 p-4 text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-900">
                    {{ session('error') }}
                </div>
            @endif

            @if($patient)
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-[#0F172A]">Informasi Pasien</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Nama:</span>
                            <span class="font-medium">{{ $patient->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">NIK:</span>
                            <span class="font-medium">{{ $patient->nik }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">No. BPJS:</span>
                            <span class="font-medium">{{ $patient->bpjs_card_no }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Status BPJS saat ini:</span>
                            <span class="font-medium">{{ $patient->meta['bpjs_status'] ?? 'TIDAK DIKETAHUI' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#64748B]">Kelas BPJS saat ini:</span>
                            <span class="font-medium">{{ $patient->meta['bpjs_class'] ?? 'TIDAK DIKETAHUI' }}</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <form method="POST" action="{{ route('bpjs.debug.fix-status') }}">
                            @csrf
                            <button type="submit" 
                                    class="rounded-lg bg-red-600 px-6 py-3 text-sm font-semibold text-white hover:bg-red-700">
                                Perbaiki Status (TIDAK AKTIF)
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <p class="text-[#64748B]">Pasien AHMAD DAHLAN tidak ditemukan dalam database.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>