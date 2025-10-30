<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-[24px] font-bold text-[#0F172A]">Data Pasien</h1>
                <p class="mt-1 text-[14px] text-[#6B7280]">Kelola data dan riwayat pasien puskesmas</p>
            </div>
            @can('patient.create')
                <a href="{{ route('patients.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 !h-[52px] rounded-[12px] bg-[#2563EB] text-white font-semibold text-[15px] hover:bg-[#1d4ed8] transition-colors shadow-sm">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Pasien Baru
                </a>
            @endcan
        </div>
    </x-slot>

    <section class="bg-white rounded-[18px] shadow-sm border border-[#E2E8F0] overflow-hidden">
        {{-- Search and Filter Section --}}
        <div class="p-6 border-b border-[#E2E8F0]">
            <form method="GET" class="flex flex-col gap-4 md:flex-row md:items-end">
                {{-- Search Input --}}
                <div class="flex-1">
                    <label for="search" class="block text-[14px] font-semibold text-[#0F172A] mb-2">CARI PASIEN</label>
                    <input 
                        type="search"
                        id="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="No. RM, Nama, NIK, atau No. BPJS..."
                        data-autocomplete="patients"
                        data-autocomplete-url="{{ route('search.suggestions') }}"
                        data-autocomplete-submit="true"
                        class="w-full h-[44px] px-4 text-[15px] text-[#0F172A] placeholder-[#94A3B8] bg-white border border-[#E2E8F0] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all" />
                </div>

                {{-- Payment Type Filter --}}
                <div class="w-full md:w-48">
                    <label for="payment_type" class="block text-[14px] font-semibold text-[#0F172A] mb-2">PEMBIAYAAN</label>
                    <select 
                        id="payment_type"
                        name="payment_type"
                        class="w-full h-[44px] px-4 text-[15px] text-[#0F172A] bg-white border border-[#E2E8F0] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all">
                        <option value="">Semua</option>
                        <option value="bpjs" @selected(request('payment_type') == 'bpjs')>BPJS</option>
                        <option value="umum" @selected(request('payment_type') == 'umum')>Umum</option>
                    </select>
                </div>

                {{-- Per Page Filter --}}
                <div class="w-full md:w-44">
                    <label for="per_page" class="block text-[14px] font-semibold text-[#0F172A] mb-2">TAMPILKAN</label>
                    <select 
                        id="per_page"
                        name="per_page"
                        class="w-full h-[44px] px-4 text-[15px] text-[#0F172A] bg-white border border-[#E2E8F0] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all">
                        @foreach ([5, 10, 25, 50] as $size)
                            <option value="{{ $size }}" @selected(request('per_page', 5) == $size)>{{ $size }} per halaman</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[16px] font-semibold text-[#0F172A]">
                    Daftar Pasien ({{ $patients->total() }} total)
                </h2>
                <button type="button" 
                        onclick="window.location.href='{{ route('patients.index') }}'"
                        class="inline-flex items-center gap-2 px-4 h-[36px] rounded-[8px] border border-[#E2E8F0] text-[14px] text-[#6B7280] font-medium hover:bg-[#F8FAFC] transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    Filter Lanjutan
                </button>
            </div>

            @if ($patients->count())
                <div class="overflow-x-auto rounded-[12px] border border-[#E2E8F0]">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">No. RM</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Nama Pasien</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">NIK</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">No. BPJS</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Pembiayaan</th>
                                <th class="px-4 py-3 text-center text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Jumlah Kunjungan</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Kunjungan Terakhir</th>
                                <th class="px-4 py-3 text-center text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patients as $patient)
                                <tr class="border-b border-[#E2E8F0] last:border-0 hover:bg-[#F8FAFC] transition-colors">
                                    {{-- No. RM --}}
                                    <td class="px-4 py-4">
                                        <a href="{{ route('patients.show', $patient) }}" 
                                           class="text-[15px] font-semibold text-[#2563EB] hover:underline">
                                            {{ $patient->medical_record_number }}
                                        </a>
                                    </td>

                                    {{-- Nama Pasien --}}
                                    <td class="px-4 py-4">
                                        <div class="text-[15px] font-medium text-[#0F172A]">
                                            {{ $patient->name }}
                                        </div>
                                    </td>

                                    {{-- NIK --}}
                                    <td class="px-4 py-4">
                                        <div class="text-[14px] text-[#6B7280]">
                                            {{ $patient->nik }}
                                        </div>
                                    </td>

                                    {{-- No. BPJS --}}
                                    <td class="px-4 py-4">
                                        <div class="text-[14px] text-[#6B7280]">
                                            {{ $patient->bpjs_card_no ?: '-' }}
                                        </div>
                                    </td>

                                    {{-- Pembiayaan --}}
                                    <td class="px-4 py-4">
                                        @if($patient->bpjs_card_no)
                                            <span class="inline-flex items-center px-3 py-1 rounded-[6px] text-[13px] font-semibold bg-[#2563EB] text-white">
                                                BPJS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-[6px] text-[13px] font-semibold bg-[#6B7280] text-white">
                                                Umum
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Jumlah Kunjungan --}}
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-[15px] font-semibold text-[#0F172A]">
                                            {{ number_format($patient->visits_count) }}
                                        </span>
                                    </td>

                                    {{-- Kunjungan Terakhir --}}
                                    <td class="px-4 py-4">
                                        <div class="text-[14px] text-[#6B7280]">
                                            {{ optional($patient->latest_visit?->visit_datetime)?->translatedFormat('d/m/Y') ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('patients.show', $patient) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-[6px] text-[13px] font-medium text-[#0F172A] hover:bg-[#EEF2FF] transition-colors">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-[#94A3B8]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <p class="mt-3 text-[15px] text-[#6B7280]">Belum ada data pasien yang cocok.</p>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if ($patients->hasPages())
            <div class="border-t border-[#E2E8F0] px-6 py-4">
                {{ $patients->withQueryString()->links() }}
            </div>
        @endif
    </section>
</x-app-layout>
