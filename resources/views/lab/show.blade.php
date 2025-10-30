<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] py-6">
        <div class="mx-auto max-w-5xl px-6">
            {{-- Header --}}
            <div class="mb-6 flex items-center gap-4">
                <a href="{{ route('lab.index') }}" class="text-[#6B7280] hover:text-[#0F172A]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-[#0F172A]">Detail Hasil Pemeriksaan Lab</h1>
                    <p class="text-sm text-[#6B7280] mt-1">Order: {{ $labOrder->order_number }}</p>
                </div>
                
                {{-- Edit Button untuk COMPLETED orders --}}
                @if($labOrder->status === 'COMPLETED' && Auth::user()->can('lab.result'))
                <a href="{{ route('lab.input-result', $labOrder) }}" 
                   class="inline-flex items-center gap-2 rounded-lg bg-[#2563EB] border-2 border-[#2563EB] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1D4ED8] transition-colors shadow-sm">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Hasil
                </a>
                @endif
            </div>

            {{-- Status Badge --}}
            <div class="mb-6">
                @if($labOrder->status === 'PENDING')
                <span class="inline-flex items-center gap-2 rounded-full bg-[#FEF3C7] px-4 py-2 text-sm font-semibold text-[#F59E0B]">
                    <svg class="h-5 w-5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Menunggu Input Hasil
                </span>
                @elseif($labOrder->status === 'COMPLETED')
                <span class="inline-flex items-center gap-2 rounded-full bg-[#D1FAE5] px-4 py-2 text-sm font-semibold text-[#16A34A]">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Selesai
                </span>
                @elseif($labOrder->status === 'IN_PROGRESS')
                <span class="inline-flex items-center gap-2 rounded-full bg-[#DBEAFE] px-4 py-2 text-sm font-semibold text-[#2563EB]">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sedang Diproses
                </span>
                @else
                <span class="inline-flex items-center rounded-full bg-[#E2E8F0] px-4 py-2 text-sm font-semibold text-[#6B7280]">
                    {{ $labOrder->status }}
                </span>
                @endif
            </div>

            {{-- Patient Info Card --}}
            <div class="mb-6 rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#0F172A] mb-4">Informasi Pasien</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-[#6B7280]">Nama Pasien</p>
                        <p class="font-semibold text-[#0F172A]">{{ $labOrder->visit->patient->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B7280]">No. Rekam Medis</p>
                        <p class="font-semibold text-[#2563EB]">{{ $labOrder->visit->patient->medical_record_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B7280]">Jenis Kelamin / Usia</p>
                        <p class="font-semibold text-[#0F172A]">
                            @if($labOrder->visit->patient->gender === 'MALE') Laki-laki
                            @elseif($labOrder->visit->patient->gender === 'FEMALE') Perempuan
                            @else -
                            @endif
                            • 
                            @if($labOrder->visit->patient->date_of_birth)
                                {{ \Carbon\Carbon::parse($labOrder->visit->patient->date_of_birth)->age }} tahun
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B7280]">Dokter Peminta</p>
                        <p class="font-semibold text-[#0F172A]">{{ $labOrder->orderedByUser->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-[#E2E8F0] grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-[#6B7280]">Waktu Permintaan</p>
                        <p class="font-semibold text-[#0F172A]">{{ $labOrder->requested_at->format('d F Y, H:i') }}</p>
                    </div>
                    @if($labOrder->completed_at)
                    <div>
                        <p class="text-xs text-[#6B7280]">Waktu Selesai</p>
                        <p class="font-semibold text-[#16A34A]">{{ $labOrder->completed_at->format('d F Y, H:i') }}</p>
                    </div>
                    @endif
                    @if($labOrder->clinical_notes)
                    <div class="md:col-span-{{ $labOrder->completed_at ? '1' : '2' }}">
                        <p class="text-xs text-[#6B7280]">Catatan Klinis</p>
                        <p class="text-sm text-[#0F172A]">{{ $labOrder->clinical_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Lab Results --}}
            <div class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm overflow-hidden">
                <div class="border-b border-[#E2E8F0] p-6">
                    <h3 class="text-base font-bold text-[#0F172A]">Hasil Pemeriksaan Laboratorium</h3>
                    <p class="text-sm text-[#6B7280] mt-1">Daftar tes dan hasil pemeriksaan</p>
                </div>

                <div class="p-6">
                    @if($labOrder->items->count() === 0)
                    <div class="text-center py-12">
                        <svg class="h-16 w-16 text-[#E2E8F0] mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="mt-3 text-sm font-semibold text-[#374151]">Tidak ada item pemeriksaan</p>
                        <p class="text-xs text-[#6B7280] mt-1">Belum ada tes yang diminta untuk order ini</p>
                    </div>
                    @else
                    <div class="space-y-4">
                        @foreach($labOrder->items as $index => $item)
                        <div class="bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] p-5">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#2563EB] text-white font-bold flex-shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4 mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-[#0F172A] text-lg">{{ $item->test_name }}</h4>
                                            @if($item->loinc_code)
                                            <p class="text-xs text-[#6B7280] mt-1">LOINC: {{ $item->loinc_code }}</p>
                                            @endif
                                        </div>
                                        
                                        @if($item->result_status === 'FINAL')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-[#D1FAE5] px-3 py-1 text-xs font-semibold text-[#16A34A]">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Final
                                        </span>
                                        @elseif($item->result_status === 'PRELIMINARY')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-[#FEF3C7] px-3 py-1 text-xs font-semibold text-[#F59E0B]">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            Preliminary
                                        </span>
                                        @else
                                        <span class="inline-flex items-center rounded-full bg-[#E2E8F0] px-3 py-1 text-xs font-semibold text-[#6B7280]">
                                            Belum Ada
                                        </span>
                                        @endif
                                    </div>
                                    
                                    @if($item->result)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-[#E2E8F0]">
                                        <div>
                                            <p class="text-xs text-[#6B7280] mb-1">Hasil</p>
                                            <p class="text-2xl font-bold text-[#0F172A]">
                                                {{ $item->result }}
                                                @if($item->unit)
                                                <span class="text-base font-normal text-[#6B7280]">{{ $item->unit }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        
                                        @if($item->reference_range)
                                        <div>
                                            <p class="text-xs text-[#6B7280] mb-1">Nilai Rujukan</p>
                                            <p class="text-sm font-semibold text-[#374151]">{{ $item->reference_range }}</p>
                                        </div>
                                        @endif
                                        
                                        @if($item->abnormal_flag)
                                        <div>
                                            <p class="text-xs text-[#6B7280] mb-1">Status</p>
                                            @if($item->abnormal_flag === 'NORMAL')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-[#D1FAE5] px-3 py-1 text-xs font-semibold text-[#16A34A]">
                                                ✓ Normal
                                            </span>
                                            @elseif($item->abnormal_flag === 'HIGH')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-[#FEE2E2] px-3 py-1 text-xs font-semibold text-[#DC2626]">
                                                ↑ Tinggi
                                            </span>
                                            @elseif($item->abnormal_flag === 'LOW')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-[#FEF3C7] px-3 py-1 text-xs font-semibold text-[#F59E0B]">
                                                ↓ Rendah
                                            </span>
                                            @elseif($item->abnormal_flag === 'CRITICAL')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-[#7F1D1D] px-3 py-1 text-xs font-semibold text-white">
                                                ⚠ Kritis
                                            </span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($item->resulted_at)
                                    <div class="mt-3 pt-3 border-t border-[#E2E8F0]">
                                        <p class="text-xs text-[#6B7280]">
                                            <svg class="h-3 w-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4l.707.707a1 1 0 001.414-1.414L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            Hasil diinput: {{ \Carbon\Carbon::parse($item->resulted_at)->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                    @endif
                                    @else
                                    <div class="mt-4 pt-4 border-t border-[#E2E8F0]">
                                        <p class="text-sm text-[#F59E0B] italic">⏳ Hasil belum tersedia</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Action Footer --}}
                <div class="border-t border-[#E2E8F0] p-6 bg-[#F8FAFC] flex items-center justify-between gap-3">
                    <a href="{{ route('lab.index') }}" 
                       class="inline-flex items-center gap-2 rounded-lg bg-white border-2 border-[#E2E8F0] px-6 py-3 text-sm font-semibold text-[#374151] hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Antrean Lab
                    </a>
                    
                    @if($labOrder->status === 'COMPLETED')
                    <a href="{{ route('lab.print', $labOrder) }}" 
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#2563EB] border-2 border-[#2563EB] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1D4ED8] transition-colors shadow-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak Hasil Lab
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
