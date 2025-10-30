<x-app-layout>
    <div class="flex min-h-screen bg-[#F8FAFC]" x-data="{ showSidebar: true }">
        {{-- Left Sidebar - Patient Queue --}}
        <aside class="w-80 border-r border-[#E2E8F0] bg-white" x-show="showSidebar" x-transition>
            <div class="border-b border-[#E2E8F0] p-6">
                <h3 class="text-base font-bold text-[#0F172A]">Daftar Antrian</h3>
                <p class="mt-1 text-sm text-[#6B7280]">{{ $visit->clinic_name }}</p>
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
                        <a href="{{ route('visits.show', $queueVisit) }}" 
                           class="flex items-center gap-3 rounded-xl border p-3 transition-all {{ $queueVisit->id === $visit->id ? 'border-[#2563EB] bg-[#EEF2FF]' : 'border-[#E2E8F0] bg-white hover:border-[#C7D2FE]' }}">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-[#2563EB] text-sm font-bold text-white">
                                {{ substr($queueVisit->patient->name, 0, 2) }}
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
                                <p class="text-xs text-[#6B7280]">{{ $queueVisit->queue_number }} • RM: {{ $queueVisit->patient->medical_record_number }}</p>
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
                <div class="mb-6 flex items-center justify-between rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-4">
                        <button @click="showSidebar = !showSidebar" class="text-[#6B7280] hover:text-[#0F172A]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[#2563EB] text-xl font-bold text-white">
                            {{ substr($visit->patient->name, 0, 2) }}
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-[#0F172A]">{{ $visit->patient->name }}</h1>
                            <p class="text-sm text-[#6B7280]">RM: {{ $visit->patient->medical_record_number }} • Antrian: {{ $visit->queue_number ?: 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($visit->coverage_type === 'BPJS')
                            <span class="inline-flex items-center gap-1 rounded-xl bg-[#DBEAFE] px-4 py-2 text-sm font-semibold text-[#2563EB]">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                BPJS Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-xl bg-[#FEF3C7] px-4 py-2 text-sm font-semibold text-[#F59E0B]">
                                Umum
                            </span>
                        @endif
                        <a href="{{ route('patients.show', $visit->patient_id) }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-[#E2E8F0] px-4 text-sm font-semibold text-[#6B7280] hover:bg-[#F8FAFC]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Detail Pasien
                        </a>
                    </div>
                </div>

                {{-- Form SOAP --}}
                @can('emr.create')
                    <form method="POST" action="{{ route('emr.store', $visit) }}" class="mb-6 space-y-6">
                        @csrf
                        
                        {{-- TANDA VITAL --}}
                        <div class="rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-bold text-[#0F172A]">TANDA VITAL</h2>
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold uppercase text-[#6B7280]">Keadaan Umum</label>
                                    <select name="general_condition" class="h-11 w-full rounded-xl border border-[#E2E8F0] px-4 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                        <option value="">Pilih</option>
                                        <option value="Baik">Baik</option>
                                        <option value="Cukup">Cukup</option>
                                        <option value="Lemah">Lemah</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold uppercase text-[#6B7280]">Nadi (x/menit)</label>
                                    <input type="number" name="pulse" placeholder="80" class="h-11 w-full rounded-xl border border-[#E2E8F0] px-4 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold uppercase text-[#6B7280]">Suhu (°C)</label>
                                    <input type="number" step="0.1" name="temperature" placeholder="36.5" class="h-11 w-full rounded-xl border border-[#E2E8F0] px-4 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold uppercase text-[#6B7280]">Pernapasan (x/menit)</label>
                                    <input type="number" name="respiration" placeholder="20" class="h-11 w-full rounded-xl border border-[#E2E8F0] px-4 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                </div>
                            </div>
                        </div>

                        {{-- SUBJECTIVE (KELUHAN) --}}
                        <div class="rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-bold text-[#0F172A]">SUBJECTIVE (KELUHAN)</h2>
                            <textarea name="subjective" rows="4" placeholder="Tulis keluhan utama pasien..." class="w-full rounded-xl border border-[#E2E8F0] px-4 py-3 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">{{ old('subjective') }}</textarea>
                            <x-input-error :messages="$errors->get('subjective')" class="mt-2" />
                        </div>

                        {{-- OBJECTIVE (PEMERIKSAAN FISIK) --}}
                        <div class="rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-bold text-[#0F172A]">OBJECTIVE (PEMERIKSAAN FISIK)</h2>
                            <textarea name="objective" rows="4" placeholder="Tulis hasil pemeriksaan fisik..." class="w-full rounded-xl border border-[#E2E8F0] px-4 py-3 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">{{ old('objective') }}</textarea>
                            <x-input-error :messages="$errors->get('objective')" class="mt-2" />
                        </div>

                        {{-- ASSESSMENT (DIAGNOSA) --}}
                        <div class="rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-bold text-[#0F172A]">ASSESSMENT (DIAGNOSA)</h2>
                            <textarea name="assessment" rows="3" placeholder="Tulis diagnosis kerja..." class="mb-4 w-full rounded-xl border border-[#E2E8F0] px-4 py-3 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">{{ old('assessment') }}</textarea>
                            <x-input-error :messages="$errors->get('assessment')" class="mb-4" />
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold uppercase text-[#6B7280]">Kode ICD-10</label>
                                    <input type="text" name="icd10_code" placeholder="A09" maxlength="10" value="{{ old('icd10_code') }}" class="h-11 w-full rounded-xl border border-[#E2E8F0] px-4 text-sm uppercase text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                    <x-input-error :messages="$errors->get('icd10_code')" class="mt-2" />
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold uppercase text-[#6B7280]">Deskripsi ICD-10</label>
                                    <input type="text" name="icd10_description" placeholder="Gastroenteritis akut" value="{{ old('icd10_description') }}" class="h-11 w-full rounded-xl border border-[#E2E8F0] px-4 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                    <x-input-error :messages="$errors->get('icd10_description')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- PLAN (RENCANA TINDAKAN) --}}
                        <div class="rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-base font-bold text-[#0F172A]">PLAN (RENCANA TINDAKAN)</h2>
                            <textarea name="plan" rows="3" placeholder="Tulis rencana terapi atau tindak lanjut..." class="mb-4 w-full rounded-xl border border-[#E2E8F0] px-4 py-3 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">{{ old('plan') }}</textarea>
                            <x-input-error :messages="$errors->get('plan')" class="mb-4" />
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="rounded-xl border border-dashed border-[#E2E8F0] p-4 text-center">
                                    <svg class="mx-auto h-8 w-8 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                    <p class="mt-2 text-sm font-semibold text-[#6B7280]">Racup Obat</p>
                                    <p class="text-xs text-[#94A3B8]">Belum ada obat</p>
                                    <a href="{{ route('emr.show', $visit) }}" class="mt-3 inline-flex h-10 items-center gap-2 rounded-xl border border-[#2563EB] bg-white px-4 text-sm font-semibold text-[#2563EB] hover:bg-[#EEF2FF]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Buka Halaman EMR Lengkap
                                    </a>
                                </div>
                                
                                <div class="rounded-xl border border-dashed border-[#E2E8F0] p-4 text-center">
                                    <svg class="mx-auto h-8 w-8 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                    <p class="mt-2 text-sm font-semibold text-[#6B7280]">Permintaan Lab</p>
                                    <p class="text-xs text-[#94A3B8]">Belum ada pemeriksaan</p>
                                    @can('lab.create')
                                        <a href="{{ route('lab-orders.create', ['visit_id' => $visit->id]) }}" class="mt-3 inline-flex h-10 items-center gap-2 rounded-xl border border-[#2563EB] bg-white px-4 text-sm font-semibold text-[#2563EB] hover:bg-[#EEF2FF]">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Tambah Pemeriksaan
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" onclick="window.history.back()" class="inline-flex h-12 items-center gap-2 rounded-xl border border-[#E2E8F0] bg-white px-6 text-sm font-semibold text-[#6B7280] hover:bg-[#F8FAFC]">
                                Batal
                            </button>
                            @can('satusehat.sync')
                                <button type="button" 
                                    class="satusehat-sync-form inline-flex h-12 items-center gap-2 rounded-xl bg-[#16A34A] px-6 text-sm font-semibold text-white hover:bg-[#15803D]"
                                    data-url="{{ route('satusehat.sync-encounter', $visit) }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Simpan & Kirim ke SATUSEHAT
                                </button>
                            @endcan
                            <button type="submit" class="inline-flex h-12 items-center gap-2 rounded-xl bg-[#2563EB] px-6 text-sm font-semibold text-white hover:bg-[#1D4ED8]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan
                            </button>
                        </div>
                    </form>
                @endcan

                {{-- RIWAYAT RME SEBELUMNYA --}}
                <div class="rounded-[18px] border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-base font-bold text-[#0F172A]">RIWAYAT RME SEBELUMNYA</h2>
                    
                    <div class="space-y-3">
                        @forelse ($visit->emrNotes as $note)
                            <article class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                                <div class="mb-3 flex items-center justify-between border-b border-[#E2E8F0] pb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-[#6B7280]" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-[#0F172A]">{{ $note->author?->name ?? __('Petugas tidak diketahui') }}</span>
                                    </div>
                                    <span class="text-xs text-[#6B7280]">{{ $note->created_at->translatedFormat('d F Y H:i') }}</span>
                                </div>
                                
                                <div class="grid gap-3 md:grid-cols-2">
                                    @if ($note->subjective)
                                        <div>
                                            <dt class="text-xs font-semibold uppercase text-[#6B7280]">Subjective</dt>
                                            <dd class="mt-1 text-sm leading-relaxed text-[#0F172A]">{{ $note->subjective }}</dd>
                                        </div>
                                    @endif
                                    @if ($note->objective)
                                        <div>
                                            <dt class="text-xs font-semibold uppercase text-[#6B7280]">Objective</dt>
                                            <dd class="mt-1 text-sm leading-relaxed text-[#0F172A]">{{ $note->objective }}</dd>
                                        </div>
                                    @endif
                                    @if ($note->assessment)
                                        <div>
                                            <dt class="text-xs font-semibold uppercase text-[#6B7280]">Assessment</dt>
                                            <dd class="mt-1 text-sm leading-relaxed text-[#0F172A]">{{ $note->assessment }}</dd>
                                        </div>
                                    @endif
                                    @if ($note->plan)
                                        <div>
                                            <dt class="text-xs font-semibold uppercase text-[#6B7280]">Plan</dt>
                                            <dd class="mt-1 text-sm leading-relaxed text-[#0F172A]">{{ $note->plan }}</dd>
                                        </div>
                                    @endif
                                </div>
                                
                                @if ($note->icd10_code)
                                    <div class="mt-3 inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs">
                                        <svg class="h-4 w-4 text-[#2563EB]" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold text-[#0F172A]">{{ $note->icd10_code }}</span>
                                        <span class="text-[#6B7280]">{{ $note->icd10_description }}</span>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <div class="rounded-xl border border-dashed border-[#E2E8F0] py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-3 text-sm font-semibold text-[#6B7280]">Belum ada catatan EMR</p>
                                <p class="text-xs text-[#94A3B8]">Isi formulir di atas untuk menambahkan catatan klinis pertama</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        (() => {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Handle SATUSEHAT sync with form submission
            document.querySelectorAll('.satusehat-sync-form').forEach((button) => {
                button.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const url = button.dataset.url;
                    const form = button.closest('form');

                    if (!url || !form) return;

                    // Submit the form first
                    const formData = new FormData(form);
                    
                    button.disabled = true;
                    button.classList.add('opacity-60');
                    const originalText = button.innerHTML;
                    button.innerHTML = '<svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';

                    try {
                        // Submit EMR form
                        const formResponse = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrf ?? '',
                                'Accept': 'application/json',
                            },
                        });

                        if (formResponse.ok) {
                            // Then trigger SATUSEHAT sync
                            button.innerHTML = '<svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mengirim ke SATUSEHAT...';
                            
                            const syncResponse = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf ?? '',
                                },
                            });

                            const result = await syncResponse.json();

                            if (result.success) {
                                window.location.reload();
                            } else {
                                alert(result.message ?? 'Gagal mengirim ke SATUSEHAT');
                                button.innerHTML = originalText;
                                button.disabled = false;
                                button.classList.remove('opacity-60');
                            }
                        } else {
                            const errorData = await formResponse.json();
                            alert(errorData.message ?? 'Gagal menyimpan catatan EMR');
                            button.innerHTML = originalText;
                            button.disabled = false;
                            button.classList.remove('opacity-60');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan: ' + error.message);
                        button.innerHTML = originalText;
                        button.disabled = false;
                        button.classList.remove('opacity-60');
                    }
                });
            });
        })();
    </script>
@endpush


@push('scripts')
    <script>
        (() => {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            document.querySelectorAll('.satusehat-sync').forEach((button) => {
                button.addEventListener('click', async () => {
                    const url = button.dataset.url;
                    const targetSelector = button.dataset.target;
                    const target = targetSelector ? document.querySelector(targetSelector) : null;

                    if (!url || !target) {
                        return;
                    }

                    button.disabled = true;
                    button.classList.add('opacity-60');
                    target.innerHTML = '{{ __('Submitting sync request...') }}';

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf ?? '',
                            },
                        });
                        const result = await response.json();

                        if (result.success) {
                            target.innerHTML = `<span class="text-emerald-700 font-semibold">${result.message ?? '{{ __('Sync request queued.') }}'}</span>`;
                        } else {
                            target.innerHTML = `<span class="text-rose-600 font-semibold">${result.message ?? '{{ __('Failed to queue sync.') }}'}</span>`;
                        }
                    } catch (error) {
                        target.innerHTML = `<span class="text-rose-600 font-semibold">${error.message}</span>`;
                    } finally {
                        button.disabled = false;
                        button.classList.remove('opacity-60');
                    }
                });
            });
        })();
    </script>
@endpush
