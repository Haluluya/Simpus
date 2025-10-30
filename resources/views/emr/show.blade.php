<x-app-layout>
    <div class="flex min-h-screen bg-[#F8FAFC]" x-data="{ showSidebar: true }">
        {{-- Left Sidebar - Patient Queue --}}
        <aside class="w-80 border-r border-[#E2E8F0] bg-white" x-show="showSidebar" x-transition>
            <div class="border-b border-[#E2E8F0] p-6">
                <h3 class="text-base font-bold text-[#0F172A]">Daftar Antrian</h3>
                <p class="mt-1 text-sm text-[#6B7280]">{{ $visit->clinic_name ?? 'Poli' }}</p>
            </div>
            
            <div class="overflow-y-auto p-4" style="max-height: calc(100vh - 120px);">
                <div class="space-y-2">
                    @php
                        $todayVisits = \App\Models\Visit::query()
                            ->where('clinic_name', $visit->clinic_name)
                            ->whereDate('visit_datetime', today())
                            ->with('patient')
                            ->orderBy('queue_number')
                            ->get();
                    @endphp
                    
                    @foreach($todayVisits as $queueVisit)
                        <a href="{{ route('emr.show', $queueVisit) }}" 
                           class="flex items-center gap-3 rounded-xl border p-3 transition-all {{ $queueVisit->id === $visit->id ? 'border-[#2563EB] bg-[#EEF2FF]' : 'border-[#E2E8F0] bg-white hover:border-[#C7D2FE]' }}">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full {{ $queueVisit->id === $visit->id ? 'bg-[#2563EB]' : 'bg-[#6B7280]' }} text-sm font-bold text-white">
                                {{ $queueVisit->queue_number ?? '-' }}
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <div class="flex items-center gap-2">
                                    <p class="truncate text-sm font-semibold text-[#0F172A]">{{ $queueVisit->patient->name }}</p>
                                    @if($queueVisit->status === 'ONGOING')
                                        <span class="inline-flex rounded-full bg-[#16A34A] px-2 py-0.5 text-xs font-semibold text-white">Aktif</span>
                                    @elseif($queueVisit->status === 'SCHEDULED')
                                        <span class="inline-flex rounded-full bg-[#F59E0B] px-2 py-0.5 text-xs font-semibold text-white">Menunggu</span>
                                    @endif
                                </div>
                                <p class="text-xs text-[#6B7280]">RM: {{ $queueVisit->patient->medical_record_number }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <div class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-7xl p-6">
                {{-- Patient Header --}}
                <div class="mb-6 rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <button @click="showSidebar = !showSidebar" class="text-[#6B7280] hover:text-[#0F172A]">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#2563EB] text-base font-bold text-white">
                                {{ $visit->queue_number ?? '-' }}
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-[#0F172A]">{{ $visit->patient->name }}</h1>
                                <p class="text-sm text-[#6B7280]">{{ $visit->patient->medical_record_number }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-block px-4 py-2 text-sm rounded-lg font-medium
                                @if($visit->status === 'ONGOING') bg-blue-100 text-blue-600
                                @elseif($visit->status === 'SCHEDULED') bg-blue-100 text-blue-600
                                @elseif($visit->status === 'COMPLETED') bg-green-100 text-green-600
                                @elseif($visit->status === 'CANCELLED') bg-red-100 text-red-600
                                @else bg-gray-100 text-gray-600
                                @endif">
                                @if($visit->status === 'ONGOING') ONGOING
                                @elseif($visit->status === 'SCHEDULED') Menunggu
                                @elseif($visit->status === 'COMPLETED') Selesai
                                @else {{ $visit->status }}
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Informasi Detail Pasien --}}
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 pt-4 mt-4 border-t border-[#E2E8F0]">
                        <div>
                            <p class="text-xs text-[#6B7280]">Tanggal Lahir</p>
                            <p class="font-medium text-[#0F172A] text-sm">
                                @if($visit->patient->date_of_birth)
                                    {{ \Carbon\Carbon::parse($visit->patient->date_of_birth)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-[#6B7280]">Usia</p>
                            <p class="font-medium text-[#0F172A] text-sm">
                                @if($visit->patient->date_of_birth)
                                    {{ \Carbon\Carbon::parse($visit->patient->date_of_birth)->age }} tahun
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-[#6B7280]">Jenis Kelamin</p>
                            <p class="font-medium text-[#0F172A] text-sm">
                                @if($visit->patient->gender === 'MALE') Laki-laki
                                @elseif($visit->patient->gender === 'FEMALE') Perempuan
                                @else {{ $visit->patient->gender ?? '-' }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-[#6B7280]">Poli/Unit</p>
                            <p class="font-medium text-[#0F172A] text-sm">{{ $visit->clinic_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-[#6B7280]">Jenis Pasien</p>
                            <p class="font-medium text-[#0F172A] text-sm">{{ $visit->coverage_type ?? 'Umum' }}</p>
                        </div>
                    </div>
                </div>

            {{-- Riwayat RME Sebelumnya --}}
            @if($visit->emrNotes->isNotEmpty())
            <div class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm mb-6">
                <div class="border-b border-[#E2E8F0] p-6">
                    <h3 class="text-base font-bold text-[#0F172A]">Riwayat Catatan Medis</h3>
                    <p class="text-xs text-[#6B7280] mt-1">Catatan kunjungan sebelumnya</p>
                </div>
                <div class="p-6 space-y-3">
                    @foreach($visit->emrNotes as $note)
                    <div class="border border-[#E2E8F0] bg-[#F8FAFC] rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3 pb-3 border-b border-[#E2E8F0]">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-[#2563EB] to-[#1D4ED8] text-xs font-bold text-white shadow-sm">
                                    {{ substr($note->author->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-[#0F172A]">{{ $note->author->name }}</p>
                                    <p class="text-xs text-[#6B7280]">{{ $note->created_at->format('d M Y, H:i') }} WIB</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex gap-2">
                                <span class="font-bold text-[#2563EB] min-w-[20px]">S:</span>
                                <p class="text-[#374151] flex-1">{{ $note->subjective }}</p>
                            </div>
                            <div class="flex gap-2">
                                <span class="font-bold text-[#16A34A] min-w-[20px]">O:</span>
                                <p class="text-[#374151] flex-1">{{ $note->objective }}</p>
                            </div>
                            <div class="flex gap-2">
                                <span class="font-bold text-[#F59E0B] min-w-[20px]">A:</span>
                                <div class="flex-1">
                                    <p class="text-[#374151]">{{ $note->assessment }}</p>
                                    <span class="inline-block mt-1.5 text-xs bg-[#DBEAFE] text-[#2563EB] px-2 py-1 rounded-md font-medium">
                                        {{ $note->icd10_code }} - {{ $note->icd10_description }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <span class="font-bold text-[#DC2626] min-w-[20px]">P:</span>
                                <p class="text-[#374151] flex-1">{{ $note->plan }}</p>
                            </div>
                            @if($note->notes)
                            <div class="mt-3 pt-3 border-t border-[#E2E8F0]">
                                <p class="text-xs text-[#6B7280] italic">{{ $note->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Lab Orders Sebelumnya --}}
            @if($visit->labOrders->isNotEmpty())
            <div class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] p-6">
                    <h3 class="text-lg font-bold text-[#0F172A]">🔬 Permintaan Lab</h3>
                </div>
                <div class="p-6 space-y-3">
                    @foreach($visit->labOrders as $labOrder)
                    <div class="border border-[#E2E8F0] rounded-xl p-4 bg-[#F8FAFC]">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm text-[#6B7280]">{{ $labOrder->created_at->format('d/m/Y H:i') }}</p>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($labOrder->status === 'COMPLETED') bg-green-100 text-green-800
                                @elseif($labOrder->status === 'PENDING') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $labOrder->status }}
                            </span>
                        </div>
                        @if($labOrder->results->isNotEmpty())
                        <div class="bg-white rounded-lg p-3">
                            <p class="font-semibold text-sm mb-2 text-[#0F172A]">Hasil Pemeriksaan:</p>
                            <ul class="space-y-1">
                                @foreach($labOrder->results as $result)
                                <li class="text-sm flex justify-between items-center py-1 border-b border-[#E2E8F0] last:border-0">
                                    <span class="font-medium">{{ $result->nama_tes }}</span>
                                    <span><strong class="text-[#2563EB]">{{ $result->hasil }}</strong> <span class="text-xs text-[#6B7280]">(Rujukan: {{ $result->nilai_rujukan }})</span></span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Prescriptions Sebelumnya --}}
            @if($visit->prescriptions->isNotEmpty())
            <div class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] p-6">
                    <h3 class="text-lg font-bold text-[#0F172A]">💊 Resep Obat</h3>
                </div>
                <div class="p-6 space-y-3">
                    @foreach($visit->prescriptions as $prescription)
                    <div class="border border-[#E2E8F0] rounded-xl p-4 bg-[#F8FAFC]">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm text-[#6B7280]">{{ $prescription->created_at->format('d/m/Y H:i') }}</p>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($prescription->status === 'COMPLETED') bg-green-100 text-green-800
                                @elseif($prescription->status === 'PENDING') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $prescription->status }}
                            </span>
                        </div>
                        @if($prescription->items->isNotEmpty())
                        <div class="bg-white rounded-lg p-3">
                            <ul class="space-y-2">
                                @foreach($prescription->items as $item)
                                <li class="flex justify-between items-center py-2 border-b border-[#E2E8F0] last:border-0">
                                    <div>
                                        <p class="font-semibold text-sm text-[#0F172A]">{{ $item->medicine->nama_obat }}</p>
                                        <p class="text-xs text-[#6B7280]">Dosis: {{ $item->dosis }}</p>
                                    </div>
                                    <span class="px-3 py-1 bg-[#DBEAFE] text-[#2563EB] rounded-full text-sm font-semibold">{{ $item->jumlah }} {{ $item->medicine->satuan }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if($prescription->catatan)
                        <p class="text-sm text-[#6B7280] mt-3 p-2 bg-[#F8FAFC] rounded-lg border border-[#E2E8F0]">📝 {{ $prescription->catatan }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Referrals Sebelumnya --}}
            @if($visit->referrals->isNotEmpty())
            <div class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] p-6">
                    <h3 class="text-lg font-bold text-[#0F172A]">🏥 Rujukan</h3>
                </div>
                <div class="p-6 space-y-3">
                    @foreach($visit->referrals as $referral)
                    <div class="border border-[#E2E8F0] rounded-xl p-4 bg-[#F8FAFC]">
                        <div class="space-y-2">
                            <div class="flex items-start gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#DC2626] text-white font-bold">→</span>
                                <div class="flex-1">
                                    <p class="font-bold text-[#0F172A]">{{ $referral->referred_to_facility }}</p>
                                    <p class="text-sm text-[#6B7280]">{{ $referral->referred_to_department }}</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-3 mt-3">
                                <p class="text-sm"><span class="font-semibold text-[#0F172A]">Alasan:</span> {{ $referral->reason }}</p>
                            </div>
                            @if($referral->nomor_rujukan_bpjs)
                            <div class="bg-[#DBEAFE] rounded-lg p-3">
                                <p class="text-sm font-semibold text-[#2563EB]">No. Rujukan BPJS: {{ $referral->nomor_rujukan_bpjs }}</p>
                            </div>
                            @endif
                            <p class="text-xs text-[#6B7280] mt-2">📅 {{ $referral->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Form Input RME Baru --}}
            @if($visit->status !== 'COMPLETED')
            <div class="rounded-[18px] border border-[#E2E8F0] bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] p-6">
                    <h3 class="text-base font-bold text-[#0F172A]">Form SOAP</h3>
                </div>
                
                <form method="POST" action="{{ route('emr.store', $visit) }}" class="p-6">
                    @csrf

                    {{-- TANDA VITAL --}}
                    <div class="mb-6 bg-[#F8FAFC] rounded-lg p-4 border border-[#E2E8F0]">
                        <p class="text-sm font-semibold text-[#374151] mb-3">TANDA VITAL</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label for="tekanan_darah" class="block text-xs text-[#6B7280] mb-1">Tekanan Darah</label>
                                <input type="text" id="tekanan_darah" name="tekanan_darah" 
                                    value="{{ old('tekanan_darah') }}"
                                    class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-center font-medium text-sm"
                                    placeholder="120/80">
                            </div>
                            <div>
                                <label for="nadi" class="block text-xs text-[#6B7280] mb-1">Nadi (x/menit)</label>
                                <input type="number" id="nadi" name="nadi" 
                                    value="{{ old('nadi') }}"
                                    class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-center font-medium text-sm"
                                    placeholder="80">
                            </div>
                            <div>
                                <label for="suhu" class="block text-xs text-[#6B7280] mb-1">Suhu (°C)</label>
                                <input type="number" step="0.1" id="suhu" name="suhu" 
                                    value="{{ old('suhu') }}"
                                    class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-center font-medium text-sm"
                                    placeholder="36.5">
                            </div>
                            <div>
                                <label for="pernafasan" class="block text-xs text-[#6B7280] mb-1">Pernafasan</label>
                                <input type="number" id="pernafasan" name="pernafasan" 
                                    value="{{ old('pernafasan') }}"
                                    class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-center font-medium text-sm"
                                    placeholder="20">
                            </div>
                        </div>
                    </div>

                    {{-- SUBJECTIVE --}}
                    <div class="mb-4">
                        <label for="subjective" class="block text-sm font-semibold text-[#374151] mb-2">SUBJECTIVE (KELUHAN)</label>
                        <textarea id="subjective" name="subjective" rows="3" required
                            class="w-full rounded-lg border-[#E2E8F0] bg-[#F8FAFC] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm"
                            placeholder="Keluhan utama pasien, riwayat penyakit sekarang...">{{ old('subjective') }}</textarea>
                        @error('subjective')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- OBJECTIVE --}}
                    <div class="mb-4">
                        <label for="objective" class="block text-sm font-semibold text-[#374151] mb-2">OBJECTIVE (PEMERIKSAAN FISIK)</label>
                        <textarea id="objective" name="objective" rows="3" required
                            class="w-full rounded-lg border-[#E2E8F0] bg-[#F8FAFC] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm"
                            placeholder="Hasil pemeriksaan fisik, temuan klinis...">{{ old('objective') }}</textarea>
                        @error('objective')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ASSESSMENT --}}
                    <div class="mb-4">
                        <label for="assessment" class="block text-sm font-semibold text-[#374151] mb-2">ASSESSMENT (DIAGNOSIS)</label>
                        <textarea id="assessment" name="assessment" rows="2" required
                            class="w-full rounded-lg border-[#E2E8F0] bg-[#F8FAFC] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm mb-3"
                            placeholder="Cari kode ICD-10...">{{ old('assessment') }}</textarea>
                        @error('assessment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="bg-[#F8FAFC] rounded-lg p-3 border border-[#E2E8F0]">
                            <p class="text-xs text-[#6B7280] mb-2">Diagnosis Keperawatan</p>
                            <input type="text" id="icd10_code" name="icd10_code" required
                                value="{{ old('icd10_code') }}"
                                class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm mb-2"
                                placeholder="Kode ICD-10">
                            @error('icd10_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <input type="text" id="icd10_description" name="icd10_description" required
                                value="{{ old('icd10_description') }}"
                                class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm"
                                placeholder="Deskripsi diagnosis">
                            @error('icd10_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <p class="text-xs text-[#DC2626] mt-2">J06.9 - Infeksi Saluran Pernafasan Akut (ISPA)</p>
                        </div>
                    </div>

                    {{-- PLAN --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-[#374151] mb-2">PLAN (RENCANA TINDAKAN)</label>
                        
                        {{-- Resep Obat --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs text-[#6B7280]">Resep Obat</p>
                                <button type="button" onclick="window.addPrescription()" 
                                    class="inline-flex items-center gap-1 text-xs text-[#2563EB] hover:text-[#1D4ED8] font-medium">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Tambah Obat
                                </button>
                            </div>
                            <div id="prescriptions-container" class="space-y-2"></div>
                        </div>

                        {{-- Pemeriksaan Lab --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs text-[#6B7280]">Pemeriksaan Lab</p>
                                <button type="button" onclick="window.addLabTest()" 
                                    class="inline-flex items-center gap-1 text-xs text-[#2563EB] hover:text-[#1D4ED8] font-medium">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Tambah Pemeriksaan
                                </button>
                            </div>
                            <div id="lab-tests-container" class="space-y-2"></div>
                        </div>

                        {{-- Pilih jenis pemeriksaan / rencana tindakan --}}
                        <div>
                            <label for="plan" class="block text-xs text-[#6B7280] mb-2">Rencana Tindakan Lainnya</label>
                            <textarea id="plan" name="plan" rows="2" required
                                class="w-full rounded-lg border-[#E2E8F0] bg-[#F8FAFC] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm"
                                placeholder="Rencana tindakan, edukasi, kontrol ulang...">{{ old('plan') }}</textarea>
                            @error('plan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Rujukan (Optional) --}}
                    <div class="mb-4 bg-[#F8FAFC] rounded-lg p-4 border border-[#E2E8F0]">
                        <p class="text-sm font-semibold text-[#374151] mb-3">Rujukan (Opsional)</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label for="referral_tujuan" class="block text-xs text-[#6B7280] mb-1">Tujuan Rujukan</label>
                                <input type="text" id="referral_tujuan" name="referral_tujuan"
                                    value="{{ old('referral_tujuan') }}"
                                    class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm"
                                    placeholder="Nama RS/Klinik tujuan">
                            </div>
                            <div>
                                <label for="referral_type" class="block text-xs text-[#6B7280] mb-1">Jenis Rujukan</label>
                                <select id="referral_type" name="referral_type"
                                    class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="INTERNAL">Internal</option>
                                    <option value="EXTERNAL">External</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="referral_catatan" class="block text-xs text-[#6B7280] mb-1">Catatan</label>
                                <textarea id="referral_catatan" name="referral_catatan" rows="2"
                                    class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm"
                                    placeholder="Alasan rujukan...">{{ old('referral_catatan') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Tambahan --}}
                    <div class="mb-4">
                        <div class="bg-[#F8FAFC] rounded-lg p-4 border border-[#E2E8F0]">
                            <label for="notes" class="block text-xs text-[#6B7280] mb-2">Catatan Tambahan (Opsional)</label>
                            <textarea id="notes" name="notes" rows="2"
                                class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm"
                                placeholder="Catatan penting lainnya...">{{ old('notes') }}</textarea>
                            @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex flex-col sm:flex-row items-center gap-3 pt-4 border-t border-[#E2E8F0]">
                        <button type="button" 
                            onclick="if(confirm('Yakin ingin membatalkan? Data yang diinput akan hilang.')) window.location.href='{{ route('dashboard') }}'"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-white border border-[#E2E8F0] px-5 py-2.5 text-sm font-medium text-[#6B7280] hover:bg-[#F8FAFC] transition-colors">
                            Batal
                        </button>
                        
                        <button type="submit" 
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#2563EB] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#1D4ED8] shadow-sm transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan
                        </button>
                        
                        <button type="button" 
                            onclick="alert('Fitur sync ke SATUSEHAT akan dijalankan')"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#16A34A] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#15803D] shadow-sm transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan & Kirim ke SATUSEHAT
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="rounded-xl bg-[#EEF2FF] border border-[#C7D2FE] p-6 flex items-center gap-3">
                <svg class="h-8 w-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-bold text-[#1E40AF]">Kunjungan Sudah Selesai</p>
                    <p class="text-sm text-[#3B82F6]">Kunjungan ini sudah ditandai selesai. Tidak dapat menambahkan catatan baru.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let labTestCounter = 1;
        let prescriptionCounter = 1;
        
        // Data obat untuk dropdown
        const medicines = @json($medicines ?? []);
        
        console.log('✅ EMR Script loaded successfully');
        console.log('📦 Medicines available:', medicines ? medicines.length : 0);
        
        // Make functions global
        window.addLabTest = function() {
            console.log('addLabTest called');
            const container = document.getElementById('lab-tests-container');
            if (!container) {
                console.error('lab-tests-container not found!');
                return;
            }
            
            const newItem = document.createElement('div');
            newItem.className = 'lab-test-item bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3';
            newItem.innerHTML = `
                <div class="flex gap-2 items-center">
                    <input type="text" name="lab_tests[]" 
                        class="flex-1 rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm"
                        placeholder="Nama pemeriksaan lab">
                    <button type="button" onclick="window.removeLabTest(this)" 
                        class="p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(newItem);
            labTestCounter++;
            console.log('Lab test added, total:', labTestCounter);
        };

        window.removeLabTest = function(button) {
            const container = document.getElementById('lab-tests-container');
            const item = button.closest('.lab-test-item');
            item.remove();
        };

        window.addPrescription = function() {
            console.log('addPrescription called, counter:', prescriptionCounter);
            const container = document.getElementById('prescriptions-container');
            if (!container) {
                console.error('prescriptions-container not found!');
                return;
            }
            
            const newItem = document.createElement('div');
            newItem.className = 'prescription-item bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3';
            
            // Build options HTML
            let optionsHtml = '<option value="">-- Pilih Obat --</option>';
            if (medicines && medicines.length > 0) {
                medicines.forEach(medicine => {
                    optionsHtml += `<option value="${medicine.id}">${medicine.nama_obat}</option>`;
                });
            }
            
            newItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs text-[#6B7280] mb-1">Nama Obat</label>
                        <select name="prescriptions[${prescriptionCounter}][medicine_id]" 
                            class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm">
                            ${optionsHtml}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-[#6B7280] mb-1">Dosis</label>
                        <input type="text" name="prescriptions[${prescriptionCounter}][dosis]"
                            class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm"
                            placeholder="3x1">
                    </div>
                    <div>
                        <label class="block text-xs text-[#6B7280] mb-1">Jumlah</label>
                        <input type="number" name="prescriptions[${prescriptionCounter}][jumlah]" min="1"
                            class="w-full rounded-lg border-[#E2E8F0] shadow-sm focus:border-[#2563EB] focus:ring-[#2563EB] text-sm"
                            placeholder="10">
                    </div>
                </div>
                <div class="flex justify-end mt-3">
                    <button type="button" onclick="window.removePrescription(this)" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </div>
            `;
            container.appendChild(newItem);
            prescriptionCounter++;
            console.log('Prescription added successfully, counter:', prescriptionCounter);
        };

        window.removePrescription = function(button) {
            const container = document.getElementById('prescriptions-container');
            const item = button.closest('.prescription-item');
            item.remove();
        };
    });
</script>
</x-app-layout>
