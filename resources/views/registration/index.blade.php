<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="page-title">{{ __('Pendaftaran & Antrian') }}</h1>
                <p class="mt-1 text-[13px] text-[#94A3B8]">Kelola pendaftaran pasien dan antrian kunjungan</p>
            </div>
            <x-button :href="route('patients.create')" icon="plus" class="!h-[52px] hover:!bg-[#1e3a8a] hover:!text-white">
                {{ __('Pasien Baru') }}
            </x-button>
        </div>
    </x-slot>

    {{-- Tab Navigation --}}
    <div x-data="{ activeTab: '{{ request('tab', 'pendaftaran') }}' }" class="space-y-6">
        <div class="card">
            <div class="card-body !p-0">
                <div class="flex border-b border-[#E2E8F0]">
                    <button
                        @click="activeTab = 'pendaftaran'"
                        :class="activeTab === 'pendaftaran' ? 'border-b-2 border-[#2563EB] text-[#2563EB]' : 'text-[#6B7280] hover:text-[#0F172A]'"
                        class="px-6 py-4 text-[15px] font-semibold transition-colors">
                        Pendaftaran
                    </button>
                    <button
                        @click="activeTab = 'antrian'"
                        :class="activeTab === 'antrian' ? 'border-b-2 border-[#2563EB] text-[#2563EB]' : 'text-[#6B7280] hover:text-[#0F172A]'"
                        class="px-6 py-4 text-[15px] font-semibold transition-colors">
                        Antrian Hari Ini
                    </button>
                </div>
            </div>
        </div>

        {{-- Tab Content: Pendaftaran --}}
        <div x-show="activeTab === 'pendaftaran'" class="space-y-6">
            {{-- Cari Pasien Section --}}
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">Cari Pasien</h2>
                </div>
                <div class="card-body !py-6">
                    {{-- Search Form --}}
                    <form method="GET" class="mb-6">
                        <input type="hidden" name="tab" value="pendaftaran">
                        <label for="search" class="text-[14px] font-semibold text-[#0F172A] mb-2 block">CARI PASIEN (NIK / NO. RM / NAMA)</label>
                        <div class="flex items-center rounded-[12px] border border-[#E2E8F0] bg-white shadow-sm">
                            <div class="relative flex-1">
                                <input
                                    id="search"
                                    name="search"
                                    type="search"
                                    placeholder="Masukkan NIK, No. RM, atau nama pasien"
                                    value="{{ $search }}"
                                    data-autocomplete="patients"
                                    data-autocomplete-url="{{ route('search.suggestions') }}"
                                    data-autocomplete-submit="true"
                                    class="h-[44px] w-full border-0 px-4 text-[14px] placeholder-[#94A3B8] focus:ring-0 focus:outline-none"
                                >
                            </div>
                            <button type="submit" class="inline-flex h-[44px] items-center gap-2 bg-[#2563EB] px-5 font-semibold text-white hover:bg-[#1D4ED8] focus:outline-none">
                                <x-icon.magnifying-glass class="h-5 w-5" />
                                <span>Cari</span>
                            </button>
                        </div>
                    </form>

                    @if ($search && $patients->isNotEmpty())
                        {{-- Patient Details Display --}}
                        @php $patient = $patients->first(); @endphp
                        <div class="bg-[#EEF2FF] rounded-[12px] p-4 mb-6">
                            <div class="grid grid-cols-2 gap-4 text-[14px]">
                                <div>
                                    <p class="text-[#6B7280] mb-1">Nama Pasien</p>
                                    <p class="font-semibold text-[#0F172A]">{{ $patient->name }}</p>
                                </div>
                                <div>
                                    <p class="text-[#6B7280] mb-1">No. Rekam Medis</p>
                                    <p class="font-semibold text-[#0F172A]">{{ $patient->medical_record_number }}</p>
                                </div>
                                <div>
                                    <p class="text-[#6B7280] mb-1">NIK</p>
                                    <p class="font-semibold text-[#0F172A]">{{ $patient->nik }}</p>
                                </div>
                                <div>
                                    <p class="text-[#6B7280] mb-1">No. BPJS</p>
                                    <p class="font-semibold text-[#0F172A]">{{ $patient->bpjs_card_no ?: '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- BPJS Verification --}}
                        @if ($patient->bpjs_card_no)
                            <div class="border border-[#E2E8F0] rounded-[12px] p-4 mb-6">
                                <h3 class="text-[15px] font-semibold text-[#0F172A] mb-4">Verifikasi Peserta BPJS</h3>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="text-[14px] font-semibold text-[#0F172A] mb-2 block">NO. KARTU BPJS</label>
                                        <input type="text" value="{{ $patient->bpjs_card_no }}" class="input !h-[44px] !rounded-[12px]" readonly>
                                    </div>
                                    <div>
                                        <label class="text-[14px] font-semibold text-[#0F172A] mb-2 block">TANGGAL KUNJUNGAN</label>
                                        <input type="date" 
                                               id="bpjs-service-date-{{ $patient->id }}"
                                               value="{{ now()->format('Y-m-d') }}" 
                                               class="input !h-[44px] !rounded-[12px]">
                                    </div>
                                </div>
                                <button type="button" 
                                        class="bpjs-verify-btn inline-flex items-center justify-center gap-2 px-4 h-[44px] rounded-[12px] bg-[#2563EB] text-white font-semibold text-[14px] hover:bg-[#1d4ed8] transition-colors"
                                        data-patient-id="{{ $patient->id }}"
                                        data-bpjs-card="{{ $patient->bpjs_card_no }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Verifikasi Peserta
                                </button>
                                
                                <!-- Status Hasil Verifikasi -->
                                <div id="bpjs-result-{{ $patient->id }}" class="mt-4 hidden">
                                    <!-- Result will be inserted here via JavaScript -->
                                </div>
                            </div>
                        @endif

                        {{-- Create Visit Form --}}
                        <form method="POST" action="{{ route('registrations.queue.store') }}" class="border border-[#E2E8F0] rounded-[12px] p-4">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                            <input type="hidden" name="tanggal_antrian" value="{{ $selectedDate }}">
                            
                            <h3 class="text-[15px] font-semibold text-[#0F172A] mb-4">Buat Kunjungan Baru</h3>
                            
                            <div x-data="{
                                doctorsByPoli: @js($doctorsByPoli ?? []),
                                selectedDepartment: '{{ old('department', $selectedDepartment ?? '') }}',
                                get availableDoctors() {
                                    return this.doctorsByPoli[this.selectedDepartment] || [];
                                }
                            }">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="department" class="text-[14px] font-semibold text-[#0F172A] mb-2 block">POLI TUJUAN</label>
                                        <select
                                            id="department"
                                            name="department"
                                            x-model="selectedDepartment"
                                            class="input !h-[44px] !rounded-[12px]"
                                            required>
                                            <option value="">Pilih poli</option>
                                            @foreach ($poliOptions as $department)
                                                <option value="{{ $department }}"
                                                        data-next="{{ $queueNumberMap[$department] ?? '' }}"
                                                        @selected(old('department', $selectedDepartment) === $department)>
                                                    {{ $department }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="payment_method" class="text-[14px] font-semibold text-[#0F172A] mb-2 block">JENIS PEMBIAYAAN</label>
                                        <select id="payment_method" name="payment_method" class="input !h-[44px] !rounded-[12px]" required>
                                            <option value="">Pilih pembiayaan</option>
                                            <option value="BPJS">BPJS</option>
                                            <option value="Umum">Umum</option>
                                        </select>
                                    </div>
                                    <div x-show="selectedDepartment && availableDoctors.length > 0" x-transition>
                                        <label for="doctor" class="text-[14px] font-semibold text-[#0F172A] mb-2 block">DOKTER</label>
                                        <select id="doctor" name="doctor" class="input !h-[44px] !rounded-[12px]">
                                            <option value="">Pilih dokter</option>
                                            <template x-for="doctor in availableDoctors" :key="doctor">
                                                <option :value="doctor" x-text="doctor" :selected="'{{ old('doctor') }}' === doctor"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="nomor_antrian" class="text-[14px] font-semibold text-[#0F172A] mb-2 block">NOMOR ANTRIAN</label>
                                        <input id="nomor_antrian"
                                               name="nomor_antrian"
                                               type="text"
                                               value="{{ old('nomor_antrian', $nextQueueNumber) }}"
                                               class="input !h-[44px] !rounded-[12px] bg-[#F8FAFC] uppercase font-bold text-center text-sky-700"
                                               readonly
                                               data-default="{{ $nextQueueNumber }}">
                                        <p class="text-[12px] text-[#94A3B8] mt-1">Nomor terisi otomatis sesuai poli dan tanggal.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <x-button type="submit" icon="plus" class="!h-[44px] flex-1">
                                    Simpan & Tambahkan ke Antrian
                                </x-button>
                                <a href="{{ route('registrations.sep.print', $patient) }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 !h-[44px] rounded-[12px] border-2 border-[#2563EB] text-[#2563EB] font-semibold hover:bg-[#EEF2FF] transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                                    </svg>
                                    Cetak SEP
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tab Content: Antrian Hari Ini --}}
        <div x-show="activeTab === 'antrian'" class="space-y-6">
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h2 class="section-title">Daftar Antrian - {{ \Illuminate\Support\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</h2>
                    </div>
                    <form method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="antrian">
                        <x-icon.arrow-path class="h-5 w-5 text-[#6B7280]" />
                        <button type="submit" class="text-[14px] font-semibold text-[#2563EB] hover:text-[#1D4ED8]">
                            Refresh
                        </button>
                    </form>
                </div>
                <div class="card-body !py-6">
                    @if ($queueTickets->count())
                        <div class="overflow-x-auto">
                            <table class="w-full text-[14px]">
                                <thead>
                                    <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                                        <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">No. Antrian</th>
                                        <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Nama Pasien</th>
                                        <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Poli</th>
                                        <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Dokter</th>
                                        <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Waktu Daftar</th>
                                        <th class="px-4 py-3 text-left text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Status</th>
                                        <th class="px-4 py-3 text-center text-[13px] font-semibold text-[#6B7280] uppercase tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($queueTickets as $ticket)
                                        <tr class="border-b border-[#E2E8F0] transition-colors hover:bg-[#F8FAFC]">
                                            <td class="px-4 py-4">
                                                <span class="inline-flex items-center justify-center w-16 h-10 rounded-[8px] bg-[#2563EB] text-white font-bold text-[14px]">
                                                    {{ $ticket->nomor_antrian }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="font-medium text-[#0F172A]">{{ $ticket->patient->name }}</div>
                                                <div class="text-[13px] text-[#94A3B8] mt-0.5">RM: {{ $ticket->patient->medical_record_number }}</div>
                                            </td>
                                            <td class="px-4 py-4 text-[#0F172A]">
                                                {{ $ticket->department ?? 'Poli Umum' }}
                                            </td>
                                            <td class="px-4 py-4 text-[#0F172A]">
                                                {{ $ticket->doctor ?? 'Dr. Ahmad' }}
                                            </td>
                                            <td class="px-4 py-4 text-[#0F172A]">
                                                {{ $ticket->created_at->format('H:i') }}
                                            </td>
                                            <td class="px-4 py-4">
                                                @php
                                                    $statusConfig = [
                                                        'MENUNGGU' => ['bg' => 'bg-[#DBEAFE]', 'text' => 'text-[#2563EB]', 'label' => 'Menunggu'],
                                                        'DIPANGGIL' => ['bg' => 'bg-[#FEF3C7]', 'text' => 'text-[#F59E0B]', 'label' => 'Dipanggil'],
                                                        'SELESAI' => ['bg' => 'bg-[#D1FAE5]', 'text' => 'text-[#16A34A]', 'label' => 'Selesai'],
                                                    ];
                                                    $status = $statusConfig[$ticket->status] ?? ['bg' => 'bg-[#F1F5F9]', 'text' => 'text-[#64748B]', 'label' => $ticket->status];
                                                @endphp
                                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[12px] font-medium {{ $status['bg'] }} {{ $status['text'] }}">
                                                    {{ $status['label'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center justify-center gap-2">
                                                    <form method="POST" action="{{ route('queues.update', $ticket) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="DIPANGGIL">
                                                        <input type="hidden" name="redirect_to" value="registrations.index">
                                                        <input type="hidden" name="tab" value="antrian">
                                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-[8px] bg-white border border-[#E2E8F0] text-[#0F172A] hover:bg-[#F8FAFC] transition-colors">
                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                                            </svg>
                                                            <span class="text-[14px] font-medium">Panggil</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <p class="text-[14px] text-[#94A3B8]">Belum ada antrian untuk hari ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const queueNumberMap = @json($queueNumberMap ?? []);
        const departmentSelect = document.getElementById('department');
        const queueInput = document.getElementById('nomor_antrian');

        const updateQueueNumber = () => {
            if (!queueInput) {
                return;
            }

            const selected = departmentSelect?.value || '';
            const fallback = queueInput.dataset.default || '';
            queueInput.value = queueNumberMap[selected] ?? fallback;
        };

        if (departmentSelect && queueInput) {
            updateQueueNumber();
            departmentSelect.addEventListener('change', updateQueueNumber);
        }

        // Handle BPJS Verification
        document.querySelectorAll('.bpjs-verify-btn').forEach(button => {
            button.addEventListener('click', async function() {
                const patientId = this.dataset.patientId;
                const bpjsCard = this.dataset.bpjsCard;
                const serviceDate = document.getElementById(`bpjs-service-date-${patientId}`).value;
                const resultDiv = document.getElementById(`bpjs-result-${patientId}`);
                
                // Disable button
                this.disabled = true;
                this.innerHTML = '<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memverifikasi...';
                
                try {
                    const response = await fetch('{{ route("bpjs.cek-peserta-kartu") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            no_kartu: bpjsCard,
                            service_date: serviceDate
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success && data.data.response && data.data.response.peserta) {
                        const peserta = data.data.response.peserta;
                        const isAktif = peserta.statusPeserta?.keterangan === 'AKTIF';
                        
                        resultDiv.innerHTML = `
                            <div class="p-4 rounded-[12px] border ${isAktif ? 'border-[#16A34A] bg-[#F0FDF4]' : 'border-[#DC2626] bg-[#FEF2F2]'}">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        ${isAktif ? 
                                            '<svg class="h-6 w-6 text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' :
                            '<svg class="h-6 w-6 text-[#DC2626]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>'
                                        }
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-[14px] ${isAktif ? 'text-[#16A34A]' : 'text-[#DC2626]'} mb-2">
                                            ${isAktif ? '✅ Peserta AKTIF' : '❌ Peserta TIDAK AKTIF'}
                                        </h4>
                                        <div class="space-y-1 text-[13px] text-[#0F172A]">
                                            <p><strong>Nama:</strong> ${peserta.nama || '-'}</p>
                                            <p><strong>No. Kartu:</strong> ${peserta.noKartu || '-'}</p>
                                            <p><strong>NIK:</strong> ${peserta.nik || '-'}</p>
                                            <p><strong>Kelas:</strong> ${peserta.hakKelas?.keterangan || '-'}</p>
                                            <p><strong>Status:</strong> <span class="font-semibold ${isAktif ? 'text-[#16A34A]' : 'text-[#DC2626]'}">${peserta.statusPeserta?.keterangan || '-'}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        resultDiv.classList.remove('hidden');
                    } else {
                        throw new Error(data.message || 'Verifikasi gagal');
                    }
                } catch (error) {
                    resultDiv.innerHTML = `
                        <div class="p-4 rounded-[12px] border border-[#DC2626] bg-[#FEF2F2]">
                            <p class="text-[13px] text-[#DC2626]">
                                <strong>Error:</strong> ${error.message}
                            </p>
                        </div>
                    `;
                    resultDiv.classList.remove('hidden');
                } finally {
                    // Re-enable button
                    this.disabled = false;
                    this.innerHTML = `
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Verifikasi Peserta</span>
                    `;
                }
            });
        });
    });
    </script>
    @endpush
</x-app-layout>
