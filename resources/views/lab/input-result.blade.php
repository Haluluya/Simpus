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
                <div>
                    <h1 class="text-2xl font-bold text-[#0F172A]">Input Hasil Pemeriksaan Lab</h1>
                    <p class="text-sm text-[#6B7280] mt-1">Order: {{ $labOrder->order_number }}</p>
                </div>
            </div>

            {{-- Patient Info Card --}}
            <div class="mb-6 rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#0F172A] mb-4">Informasi Pasien</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-[#6B7280]">Nama Pasien</p>
                        <p class="font-semibold text-[#0F172A]">{{ $labOrder->visit->patient->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B7280]">No. Rekam Medis</p>
                        <p class="font-semibold text-[#0F172A]">{{ $labOrder->visit->patient->medical_record_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B7280]">Dokter Peminta</p>
                        <p class="font-semibold text-[#0F172A]">{{ $labOrder->orderedByUser->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-[#E2E8F0]">
                    <p class="text-xs text-[#6B7280]">Waktu Permintaan</p>
                    <p class="font-semibold text-[#0F172A]">{{ $labOrder->requested_at->format('d F Y, H:i') }}</p>
                </div>
            </div>

            {{-- Error Messages --}}
            @if($errors->any())
            <div class="mb-6 rounded-lg bg-[#FEE2E2] border border-[#DC2626] p-4">
                <ul class="list-disc list-inside text-sm text-[#991B1B]">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Form Input Hasil --}}
            <form method="POST" action="{{ route('lab.store-result', $labOrder) }}" class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm overflow-hidden">
                @csrf
                
                <div class="border-b border-[#E2E8F0] p-6">
                    <h3 class="text-base font-bold text-[#0F172A]">Daftar Pemeriksaan</h3>
                    <p class="text-sm text-[#6B7280] mt-1">Masukkan hasil untuk setiap pemeriksaan yang diminta</p>
                </div>

                <div class="p-6 space-y-6">
                    @if($labOrder->items->count() === 0)
                    <div class="text-center py-8">
                        <svg class="h-16 w-16 text-[#E2E8F0] mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="mt-3 text-sm font-semibold text-[#374151]">Belum ada pemeriksaan yang diminta</p>
                        <p class="text-xs text-[#6B7280] mt-1">Order lab ini belum memiliki item pemeriksaan</p>
                    </div>
                    @else
                        @foreach($labOrder->items as $index => $item)
                        <div class="bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] p-5">
                            <input type="hidden" name="results[{{ $index }}][id]" value="{{ $item->id }}">
                            
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#2563EB] text-white font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-[#0F172A]">{{ $item->test_name }}</h4>
                                    @if($item->loinc_code)
                                    <p class="text-xs text-[#6B7280]">LOINC: {{ $item->loinc_code }}</p>
                                    @endif
                                    @if($item->result_status === 'FINAL')
                                    <p class="text-xs text-[#16A34A]">✓ Sudah ada hasil</p>
                                    @else
                                    <p class="text-xs text-[#F59E0B]">⏳ Belum ada hasil</p>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="result_{{ $index }}" class="block text-sm font-medium text-[#374151] mb-2">
                                        Hasil Pemeriksaan <span class="text-red-600">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="result_{{ $index }}" 
                                        name="results[{{ $index }}][result]" 
                                        required
                                        value="{{ old('results.'.$index.'.result', $item->result) }}"
                                        class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]"
                                        placeholder="Contoh: 14.5">
                                </div>

                                <div>
                                    <label for="unit_{{ $index }}" class="block text-sm font-medium text-[#374151] mb-2">
                                        Satuan
                                    </label>
                                    <input 
                                        type="text" 
                                        id="unit_{{ $index }}" 
                                        name="results[{{ $index }}][unit]" 
                                        value="{{ old('results.'.$index.'.unit', $item->unit) }}"
                                        class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]"
                                        placeholder="Contoh: mg/dL, mmol/L">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label for="reference_range_{{ $index }}" class="block text-sm font-medium text-[#374151] mb-2">
                                        Nilai Rujukan Normal
                                    </label>
                                    <input 
                                        type="text" 
                                        id="reference_range_{{ $index }}" 
                                        name="results[{{ $index }}][reference_range]" 
                                        value="{{ old('results.'.$index.'.reference_range', $item->reference_range) }}"
                                        class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]"
                                        placeholder="Contoh: 10-20 mg/dL">
                                </div>

                                <div>
                                    <label for="abnormal_flag_{{ $index }}" class="block text-sm font-medium text-[#374151] mb-2">
                                        Status Hasil
                                    </label>
                                    <select 
                                        id="abnormal_flag_{{ $index }}" 
                                        name="results[{{ $index }}][abnormal_flag]"
                                        class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB]">
                                        <option value="NORMAL" {{ old('results.'.$index.'.abnormal_flag', $item->abnormal_flag) === 'NORMAL' ? 'selected' : '' }}>Normal</option>
                                        <option value="HIGH" {{ old('results.'.$index.'.abnormal_flag', $item->abnormal_flag) === 'HIGH' ? 'selected' : '' }}>Tinggi</option>
                                        <option value="LOW" {{ old('results.'.$index.'.abnormal_flag', $item->abnormal_flag) === 'LOW' ? 'selected' : '' }}>Rendah</option>
                                        <option value="CRITICAL" {{ old('results.'.$index.'.abnormal_flag', $item->abnormal_flag) === 'CRITICAL' ? 'selected' : '' }}>Kritis</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="border-t border-[#E2E8F0] p-6 bg-[#F8FAFC] flex items-center justify-between gap-3">
                    <a href="{{ route('lab.index') }}" 
                       class="inline-flex items-center gap-2 rounded-lg bg-white border-2 border-[#E2E8F0] px-6 py-3 text-sm font-semibold text-[#374151] hover:bg-gray-50 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    
                    <button 
                        type="submit" 
                        class="inline-flex items-center gap-2 rounded-lg bg-[#16A34A] border-2 border-[#16A34A] px-8 py-3 text-sm font-semibold text-white hover:bg-[#15803D] transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Hasil Pemeriksaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
