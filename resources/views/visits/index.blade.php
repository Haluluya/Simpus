<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-[24px] font-bold text-[#0F172A]">Kunjungan & RME</h1>
                <p class="mt-1 text-[14px] text-[#6B7280]">Kelola kunjungan pasien dan rekam medis elektronik</p>
            </div>
        </div>
    </x-slot>

    <section class="bg-white rounded-[18px] shadow-sm border border-[#E2E8F0] overflow-hidden">
        {{-- Search and Filter Section --}}
        <div class="p-6 border-b border-[#E2E8F0]">
            <form method="GET" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                {{-- Search Input with Autocomplete --}}
                <div class="lg:col-span-1">
                    <label for="search" class="block text-[14px] font-semibold text-[#0F172A] mb-2">CARI KUNJUNGAN</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-[#94A3B8]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input
                            type="search"
                            id="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="No. Kunjungan, No. RM, atau Nama Pasien..."
                            data-autocomplete="visits"
                            data-autocomplete-url="{{ route('search.suggestions') }}"
                            data-autocomplete-submit="true"
                            class="w-full h-[44px] pl-12 pr-4 text-[15px] text-[#0F172A] placeholder-[#94A3B8] bg-white border border-[#E2E8F0] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all" />
                    </div>
                </div>

                {{-- Poli Filter --}}
                <div>
                    <label for="clinic_name" class="block text-[14px] font-semibold text-[#0F172A] mb-2">POLI</label>
                    <select 
                        id="clinic_name"
                        name="clinic_name"
                        class="w-full h-[44px] px-4 text-[15px] text-[#0F172A] bg-white border border-[#E2E8F0] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all">
                        <option value="">Semua Poli</option>
                        <option value="Poli Umum" @selected(($filters['clinic_name'] ?? '') === 'Poli Umum')>Poli Umum</option>
                        <option value="Poli Gigi" @selected(($filters['clinic_name'] ?? '') === 'Poli Gigi')>Poli Gigi</option>
                        <option value="Poli KIA" @selected(($filters['clinic_name'] ?? '') === 'Poli KIA')>Poli KIA</option>
                    </select>
                </div>

                {{-- Pembiayaan Filter --}}
                <div>
                    <label for="coverage_type" class="block text-[14px] font-semibold text-[#0F172A] mb-2">PEMBIAYAAN</label>
                    <select 
                        id="coverage_type"
                        name="coverage_type"
                        class="w-full h-[44px] px-4 text-[15px] text-[#0F172A] bg-white border border-[#E2E8F0] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all">
                        <option value="">Semua</option>
                        <option value="BPJS" @selected(($filters['coverage_type'] ?? '') === 'BPJS')>BPJS</option>
                        <option value="UMUM" @selected(($filters['coverage_type'] ?? '') === 'UMUM')>Umum</option>
                    </select>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 h-[44px] rounded-[12px] bg-[#2563EB] text-white font-semibold text-[14px] hover:bg-[#1d4ed8] transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        Cari
                    </button>
                </div>
            </form>

            {{-- Date Range Filters --}}
            <form method="GET" class="grid gap-4 md:grid-cols-3 mt-4">
                <div>
                    <label for="date_from" class="block text-[14px] font-semibold text-[#0F172A] mb-2">TANGGAL MULAI</label>
                    <input 
                        type="date"
                        id="date_from"
                        name="date_from"
                        value="{{ $filters['date_from'] ?? '' }}"
                        class="w-full h-[44px] px-4 text-[15px] text-[#0F172A] bg-white border border-[#E2E8F0] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all" />
                </div>

                <div>
                    <label for="date_to" class="block text-[14px] font-semibold text-[#0F172A] mb-2">TANGGAL AKHIR</label>
                    <input 
                        type="date"
                        id="date_to"
                        name="date_to"
                        value="{{ $filters['date_to'] ?? '' }}"
                        class="w-full h-[44px] px-4 text-[15px] text-[#0F172A] bg-white border border-[#E2E8F0] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all" />
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 h-[44px] rounded-[12px] border border-[#E2E8F0] text-[#0F172A] font-semibold text-[14px] hover:bg-[#F8FAFC] transition-colors">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[16px] font-semibold text-[#0F172A]">
                    Daftar Kunjungan ({{ $visits->total() }} kunjungan)
                </h2>
                <button type="button" 
                        onclick="window.location.href='{{ route('visits.index') }}'"
                        class="inline-flex items-center gap-2 px-4 h-[36px] rounded-[8px] border border-[#E2E8F0] text-[14px] text-[#6B7280] font-medium hover:bg-[#F8FAFC] transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    Filter Lanjutan
                </button>
            </div>

            @if ($visits->isEmpty())
                <div class="py-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-[#94A3B8]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 002.25 2.25h.75" />
                    </svg>
                    <p class="mt-3 text-[15px] text-[#6B7280]">Belum ada data kunjungan yang cocok.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-[12px] border border-[#E2E8F0]">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">No. Kunjungan</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">No. RM</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Nama Pasien</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Tanggal</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Poli</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Dokter</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Pembiayaan</th>
                                <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3 text-center text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visits as $visit)
                                <tr class="border-b border-[#E2E8F0] last:border-0 hover:bg-[#F8FAFC] transition-colors">
                                    {{-- No. Kunjungan --}}
                                    <td class="px-4 py-4">
                                        <a href="{{ route('visits.show', $visit) }}" 
                                           class="text-[14px] font-semibold text-[#2563EB] hover:underline">
                                            {{ $visit->visit_number ?? 'KNJ20251029001' }}
                                        </a>
                                    </td>

                                    {{-- No. RM --}}
                                    <td class="px-4 py-4">
                                        <div class="text-[14px] font-semibold text-[#0F172A]">
                                            {{ $visit->patient->medical_record_number }}
                                        </div>
                                    </td>

                                    {{-- Nama Pasien --}}
                                    <td class="px-4 py-4">
                                        <div class="text-[15px] font-medium text-[#0F172A]">
                                            {{ $visit->patient->name }}
                                        </div>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-4 py-4">
                                        <div class="text-[14px] text-[#6B7280]">
                                            {{ optional($visit->visit_datetime)->translatedFormat('d/m/Y') ?? '29/10/2025' }}
                                        </div>
                                    </td>

                                    {{-- Poli --}}
                                    <td class="px-4 py-4">
                                        <div class="text-[14px] text-[#6B7280]">
                                            {{ $visit->clinic_name ?: 'Poli Umum' }}
                                        </div>
                                    </td>

                                    {{-- Dokter --}}
                                    <td class="px-4 py-4">
                                        <div class="text-[14px] text-[#6B7280]">
                                            {{ $visit->doctor_name ?: 'Dr. Ahmad Rizki' }}
                                        </div>
                                    </td>

                                    {{-- Pembiayaan --}}
                                    <td class="px-4 py-4">
                                        @if($visit->coverage_type === 'BPJS')
                                            <span class="inline-flex items-center px-3 py-1 rounded-[6px] text-[13px] font-semibold bg-[#DBEAFE] text-[#2563EB]">
                                                BPJS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-[6px] text-[13px] font-semibold bg-[#FEF3C7] text-[#F59E0B]">
                                                Umum
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-4">
                                        @php
                                            $statusColors = [
                                                'selesai' => ['bg' => '#D1FAE5', 'text' => '#16A34A'],
                                                'dalam_proses' => ['bg' => '#FEF3C7', 'text' => '#F59E0B'],
                                                'menunggu' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
                                            ];
                                            $status = strtolower($visit->status);
                                            $colors = $statusColors[$status] ?? ['bg' => '#D1FAE5', 'text' => '#16A34A'];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-[6px] text-[13px] font-semibold"
                                              style="background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }};">
                                            {{ \Illuminate\Support\Str::headline($visit->status) }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('visits.show', $visit) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-[6px] text-[13px] font-medium text-[#0F172A] hover:bg-[#EEF2FF] border border-[#E2E8F0] transition-colors">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                </svg>
                                                Lihat RME
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if ($visits->hasPages())
            <div class="border-t border-[#E2E8F0] px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-[13px] text-[#6B7280]">
                        Menampilkan {{ $visits->firstItem() }}-{{ $visits->lastItem() }} dari {{ $visits->total() }} kunjungan
                    </div>
                    {{ $visits->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </section>
</x-app-layout>
