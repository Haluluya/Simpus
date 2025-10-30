@php
    use Illuminate\Support\Str;

    $addressSegments = array_filter([
        $patient->address,
        $patient->village,
        $patient->district,
        $patient->city,
        $patient->province,
        $patient->postal_code,
    ]);

    $genderLabel = $patient->gender ? Str::title($patient->gender) : 'Tidak diketahui';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('patients.index') }}" 
               class="inline-flex items-center justify-center w-10 h-10 rounded-[8px] border border-[#E2E8F0] text-[#6B7280] hover:bg-[#F8FAFC] transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-[24px] font-bold text-[#0F172A]">Detail Pasien</h1>
                <p class="mt-1 text-[14px] text-[#6B7280]">Informasi lengkap dan riwayat pasien</p>
            </div>
            <div class="flex gap-2">
                @can('patient.update')
                    <a href="{{ route('patients.edit', $patient) }}"
                       class="inline-flex items-center gap-2 px-4 h-[44px] rounded-[12px] border border-[#E2E8F0] text-[#0F172A] text-[14px] font-semibold hover:bg-[#F8FAFC] transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Edit Data
                    </a>
                @endcan
                @can('visit.create')
                    <a href="{{ route('visits.create', ['patient_id' => $patient->id]) }}"
                       class="inline-flex items-center gap-2 px-4 h-[44px] rounded-[12px] bg-[#2563EB] text-white text-[14px] font-semibold hover:bg-[#1d4ed8] transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Kunjungan Baru
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    {{-- Top Section: 2 Columns --}}
    <div class="grid gap-6 lg:grid-cols-2 mb-6">
        {{-- Left Column: Profil Pasien --}}
        <section class="bg-white rounded-[18px] shadow-sm border border-[#E2E8F0] overflow-hidden">
            <div class="p-6">
                <h2 class="text-[16px] font-semibold text-[#0F172A] mb-6">Profil Pasien</h2>
                
                {{-- Avatar and Basic Info --}}
                <div class="flex items-start gap-6 mb-6">
                    <div class="w-24 h-24 rounded-full bg-[#EEF2FF] flex items-center justify-center flex-shrink-0">
                        <span class="text-[32px] font-bold text-[#2563EB]">
                            {{ strtoupper(substr($patient->name, 0, 2)) }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="space-y-3">
                            <div>
                                <div class="text-[12px] text-[#6B7280] mb-1">No. Rekam Medis</div>
                                <div class="text-[14px] font-semibold text-[#0F172A]">{{ $patient->medical_record_number }}</div>
                            </div>
                            <div>
                                <div class="text-[12px] text-[#6B7280] mb-1">Nama Lengkap</div>
                                <div class="text-[14px] font-semibold text-[#0F172A]">{{ $patient->name }}</div>
                            </div>
                            <div>
                                <div class="text-[12px] text-[#6B7280] mb-1">NIK</div>
                                <div class="text-[14px] font-semibold text-[#0F172A]">{{ $patient->nik }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Additional Info Grid --}}
                <div class="space-y-3 pt-6 border-t border-[#E2E8F0]">
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#6B7280]">Tanggal Lahir</span>
                        <span class="text-[14px] font-medium text-[#0F172A]">
                            {{ optional($patient->date_of_birth)->translatedFormat('d/m/Y') ?? '—' }}
                            @if($patient->date_of_birth)
                                <span class="text-[13px] text-[#6B7280]">({{ $patient->date_of_birth->age }} tahun)</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#6B7280]">Jenis Kelamin</span>
                        <span class="text-[14px] font-medium text-[#0F172A]">{{ $genderLabel }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#6B7280]">Golongan Darah</span>
                        <span class="text-[14px] font-medium text-[#0F172A]">{{ $patient->blood_type ?: '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#6B7280]">Telepon</span>
                        <span class="text-[14px] font-medium text-[#0F172A]">{{ $patient->phone ?: '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-[#6B7280]">Email</span>
                        <span class="text-[14px] font-medium text-[#0F172A]">{{ $patient->email ?: '—' }}</span>
                    </div>
                    <div class="pt-2">
                        <div class="text-[13px] text-[#6B7280] mb-2">Alamat</div>
                        <div class="text-[14px] text-[#0F172A] leading-relaxed">
                            {{ filled($addressSegments) ? implode(', ', $addressSegments) : 'Tidak ada data alamat.' }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Right Column: Pembiayaan & Integrasi --}}
        <section class="bg-white rounded-[18px] shadow-sm border border-[#E2E8F0] overflow-hidden">
            <div class="p-6">
                <h2 class="text-[16px] font-semibold text-[#0F172A] mb-6">Pembiayaan & Integrasi</h2>
                
                {{-- BPJS Card --}}
                <div class="p-4 rounded-[12px] bg-[#EEF2FF] border border-[#C7D2FE] mb-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="text-[12px] text-[#6B7280] mb-1">Status BPJS</div>
                            <div class="text-[18px] font-bold text-[#0F172A]">{{ $patient->bpjs_card_no ?: '000123456789' }}</div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-[6px] text-[12px] font-semibold bg-[#16A34A] text-white">
                            Aktif
                        </span>
                    </div>
                    
                    @can('bpjs.verify')
                        <button type="button"
                                class="bpjs-verify w-full inline-flex items-center justify-center gap-2 px-4 h-[40px] rounded-[8px] bg-white border border-[#C7D2FE] text-[#2563EB] font-semibold text-[13px] hover:bg-[#F8FAFC] transition-colors"
                                data-url="{{ route('bpjs.cek-peserta') }}"
                                data-nik="{{ $patient->nik }}"
                                data-target="#bpjs-status">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Verifikasi Ulang
                        </button>
                        <div id="bpjs-status" class="mt-3 p-3 rounded-[8px] bg-white border border-[#E2E8F0] text-[12px] text-[#6B7280]">
                            Belum pernah waktu
                        </div>
                    @endcan
                </div>

                {{-- SATUSEHAT Card --}}
                <div class="p-4 rounded-[12px] bg-[#D1FAE5] border border-[#A7F3D0]">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="text-[12px] text-[#6B7280] mb-1">SATUSEHAT</div>
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-[14px] font-semibold text-[#0F172A]">Tersinkron</span>
                            </div>
                        </div>
                        <span class="text-[12px] text-[#6B7280]">5 menit lalu</span>
                    </div>
                    
                    @can('satusehat.sync')
                        <button type="button"
                                class="satusehat-sync w-full inline-flex items-center justify-center gap-2 px-4 h-[40px] rounded-[8px] bg-white border border-[#A7F3D0] text-[#16A34A] font-semibold text-[13px] hover:bg-[#F0FDF4] transition-colors"
                                data-url="{{ route('satusehat.sync-patient', $patient) }}"
                                data-target="#satusehat-status">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Sinkron Manual
                        </button>
                        <div id="satusehat-status" class="mt-3 p-3 rounded-[8px] bg-white border border-[#E2E8F0] text-[12px] text-[#6B7280]">
                            Menunggu permintaan sinkron.
                        </div>
                    @endcan
                </div>
            </div>
        </section>
    </div>

    {{-- Bottom Section: Tabs for Riwayat --}}
    <section class="bg-white rounded-[18px] shadow-sm border border-[#E2E8F0] overflow-hidden">
        <div class="border-b border-[#E2E8F0]">
            <div class="flex px-6" x-data="{ activeTab: '{{ request('tab', 'kunjungan') }}' }">
                <button @click="activeTab = 'kunjungan'; window.history.pushState({}, '', '?tab=kunjungan')"
                        :class="activeTab === 'kunjungan' ? 'border-b-2 border-[#2563EB] text-[#2563EB]' : 'text-[#6B7280] hover:text-[#0F172A]'"
                        class="px-4 py-4 text-[14px] font-semibold transition-colors">
                    Riwayat Kunjungan
                </button>
                <button @click="activeTab = 'rujukan'; window.history.pushState({}, '', '?tab=rujukan')"
                        :class="activeTab === 'rujukan' ? 'border-b-2 border-[#2563EB] text-[#2563EB]' : 'text-[#6B7280] hover:text-[#0F172A]'"
                        class="px-4 py-4 text-[14px] font-semibold transition-colors">
                    Riwayat Rujukan
                </button>
            </div>
        </div>

        <div x-data="{ activeTab: '{{ request('tab', 'kunjungan') }}' }">
            {{-- Tab: Riwayat Kunjungan --}}
            <div x-show="activeTab === 'kunjungan'" class="p-6">
                    </div>
                    <div class="p-5 space-y-5">
                        {{-- BPJS & SATUSEHAT Status --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[13px] font-medium text-[#6B7280]">NOMOR BPJS</span>
                                <span class="text-[14px] font-semibold text-[#0F172A]">{{ $patient->bpjs_card_no ?: '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[13px] font-medium text-[#6B7280]">STATUS SATUSEHAT</span>
                                @if($latestPatientSync?->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-[6px] text-[13px] font-semibold bg-[#16A34A] text-white">
                                        Tersinkron
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-[6px] text-[13px] font-semibold bg-[#6B7280] text-white">
                                        {{ $latestPatientSync?->status ?? 'Belum tersinkron' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Verifikasi Peserta BPJS --}}
                        @can('bpjs.verify')
                            <div class="pt-4 border-t border-[#E2E8F0]">
                                <div class="mb-4">
                                    <h3 class="text-[14px] font-semibold text-[#0F172A]">Verifikasi Peserta BPJS</h3>
                                    <p class="mt-1 text-[13px] text-[#6B7280]">Atur tanggal pelayanan kemudian klik verifikasi untuk mendapatkan status terbaru peserta.</p>
                                </div>
                                
                                <form id="bpjs-verify-form" class="space-y-3">
                                    <div>
                                        <label for="bpjs-service-date" class="block text-[13px] font-semibold text-[#0F172A] mb-2">
                                            TANGGAL PELAYANAN
                                        </label>
                                        <input id="bpjs-service-date"
                                               type="date"
                                               name="service_date"
                                               value="{{ now()->format('Y-m-d') }}"
                                               class="w-full h-[44px] px-4 text-[15px] text-[#0F172A] bg-white border border-[#E2E8F0] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all">
                                    </div>
                                    
                                    <button type="button"
                                            class="bpjs-verify w-full inline-flex items-center justify-center gap-2 px-4 h-[44px] rounded-[12px] bg-[#2563EB] text-white font-semibold text-[14px] hover:bg-[#1d4ed8] transition-colors"
                                            data-url="{{ route('bpjs.cek-peserta') }}"
                                            data-nik="{{ $patient->nik }}"
                                            data-target="#bpjs-status">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Verifikasi Peserta
                                    </button>
                                </form>

                                <div id="bpjs-status" class="mt-3 p-4 rounded-[12px] border-2 border-dashed border-[#E2E8F0] bg-[#F8FAFC] text-[13px] text-[#6B7280]">
                                    Belum ada verifikasi.
                                </div>
                            </div>
                        @endcan

                        {{-- Sinkronkan ke SATUSEHAT --}}
                        @can('satusehat.sync')
                            <div class="pt-4 border-t border-[#E2E8F0]">
                                <div class="mb-4">
                                    <h3 class="text-[14px] font-semibold text-[#0F172A]">Sinkronkan ke SATUSEHAT</h3>
                                    <p class="mt-1 text-[13px] text-[#6B7280]">Mengirim data pasien ke SATUSEHAT FHIR melalui antrean sinkronisasi.</p>
                                </div>

                                <button type="button"
                                        class="satusehat-sync w-full inline-flex items-center justify-center gap-2 px-4 h-[44px] rounded-[12px] bg-[#16A34A] text-white font-semibold text-[14px] hover:bg-[#15803d] transition-colors"
                                        data-url="{{ route('satusehat.sync-patient', $patient) }}"
                                        data-target="#satusehat-status">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Sinkron Pasien
                                </button>

                                <div id="satusehat-status" class="mt-3 p-4 rounded-[12px] border-2 border-dashed border-[#D1FAE5] bg-[#F0FDF4] text-[13px] text-[#16A34A]">
                                    Menunggu permintaan sinkron.
                                </div>
                            </div>
                        @endcan

                        {{-- Historical Info --}}
                        @if($latestBpjsClaim || $latestPatientSync)
                            <div class="pt-4 border-t border-[#E2E8F0] space-y-3">
                                @if($latestBpjsClaim)
                                    <div class="p-3 rounded-[12px] bg-[#EEF2FF] border border-[#C7D2FE]">
                                        <div class="text-[13px] font-semibold text-[#2563EB] mb-1">Verifikasi BPJS terakhir</div>
                                        <div class="text-[12px] text-[#6B7280]">
                                            {{ $latestBpjsClaim->performed_at?->diffForHumans() ?? 'Tidak diketahui' }}
                                        </div>
                                        @if(data_get($latestBpjsClaim->raw_response, 'metaData.message'))
                                            <div class="text-[12px] text-[#6B7280] mt-1">
                                                {{ data_get($latestBpjsClaim->raw_response, 'metaData.message') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if($latestPatientSync)
                                    <div class="p-3 rounded-[12px] bg-[#D1FAE5] border border-[#A7F3D0]">
                                        <div class="text-[13px] font-semibold text-[#16A34A] mb-1">Sinkron SATUSEHAT terakhir</div>
                                        <div class="text-[12px] text-[#6B7280]">
                                            {{ $latestPatientSync->last_synced_at?->diffForHumans() ?? 'Menunggu diproses' }}
                                        </div>
                                        <div class="text-[12px] text-[#6B7280] mt-1">
                                            Status: {{ $latestPatientSync->status }}
                                        </div>
                                        @if($latestPatientSync->last_error)
                                            <div class="mt-2 text-[12px] text-[#DC2626]">
                                                {{ $latestPatientSync->last_error }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>

            {{-- Tab: Riwayat Kunjungan --}}
            <div x-show="activeTab === 'kunjungan'" class="p-6">
                @if ($patient->visits->isEmpty())
                    <div class="py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-[#94A3B8]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 002.25 2.25h.75" />
                        </svg>
                        <p class="mt-3 text-[15px] font-medium text-[#0F172A]">Belum ada kunjungan</p>
                        <p class="mt-1 text-[13px] text-[#6B7280]">Tidak ditemukan riwayat kunjungan untuk pasien ini.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($patient->visits as $visit)
                            <article class="p-4 rounded-[12px] border border-[#E2E8F0] hover:bg-[#F8FAFC] transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="text-[15px] font-semibold text-[#0F172A]">
                                                {{ $visit->clinic_name ?: 'Poli Umum' }}
                                            </span>
                                            @if($visit->coverage_type === 'BPJS')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-[6px] text-[12px] font-semibold bg-[#DBEAFE] text-[#2563EB]">
                                                    BPJS
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-[6px] text-[12px] font-semibold bg-[#F3F4F6] text-[#6B7280]">
                                                    {{ $visit->coverage_type }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="text-[13px] text-[#6B7280] mb-2">
                                            <span class="font-medium">{{ $visit->doctor_name ?: 'Dr. Ahmad Rizki' }}</span>
                                        </div>
                                        
                                        <div class="text-[13px] text-[#6B7280]">
                                            <span class="font-medium">Diagnosis:</span> {{ $visit->diagnosis ?: 'ISPA' }}
                                        </div>
                                    </div>

                                    <div class="text-right flex-shrink-0">
                                        <div class="text-[14px] font-semibold text-[#0F172A] mb-1">
                                            {{ optional($visit->visit_datetime)->translatedFormat('d/m/Y') ?? '25/10/2025' }}
                                        </div>
                                        <div class="text-[13px] text-[#6B7280] mb-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[6px] text-[12px] font-semibold bg-[#DBEAFE] text-[#2563EB]">
                                                BPJS
                                            </span>
                                        </div>
                                        <a href="{{ route('visits.show', $visit) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-[6px] text-[13px] font-medium text-[#0F172A] hover:bg-[#EEF2FF] border border-[#E2E8F0] transition-colors whitespace-nowrap">
                                            Lihat Detail RME
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Tab: Riwayat Rujukan --}}
            <div x-show="activeTab === 'rujukan'" class="p-6">
                @php
                    $referralBadges = [
                        'PENDING' => ['bg' => '#FEF3C7', 'text' => '#F59E0B'],
                        'SENT' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
                        'COMPLETED' => ['bg' => '#D1FAE5', 'text' => '#16A34A'],
                        'CANCELLED' => ['bg' => '#FEE2E2', 'text' => '#DC2626'],
                    ];
                @endphp

                @if ($patient->referrals->isEmpty())
                    <div class="py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-[#94A3B8]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        <p class="mt-3 text-[15px] font-medium text-[#0F172A]">Belum ada rujukan</p>
                        <p class="mt-1 text-[13px] text-[#6B7280]">Tidak ditemukan rujukan untuk pasien ini.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($patient->referrals as $referral)
                            @php
                                $badgeColors = $referralBadges[$referral->status] ?? ['bg' => '#E5E7EB', 'text' => '#6B7280'];
                            @endphp
                            <article class="p-4 rounded-[12px] border border-[#E2E8F0] hover:bg-[#F8FAFC] transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="text-[15px] font-semibold text-[#0F172A]">
                                                {{ $referral->referred_to }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[6px] text-[12px] font-semibold"
                                                  style="background-color: {{ $badgeColors['bg'] }}; color: {{ $badgeColors['text'] }};">
                                                {{ __(ucfirst(strtolower($referral->status))) }}
                                            </span>
                                        </div>
                                        
                                        @if($referral->referred_department)
                                            <div class="text-[13px] text-[#6B7280] mb-2">
                                                <span class="font-medium">Departemen:</span> {{ $referral->referred_department }}
                                            </div>
                                        @endif
                                        
                                        @if($referral->reason)
                                            <div class="text-[13px] text-[#6B7280]">
                                                {{ Str::limit($referral->reason, 120) }}
                                            </div>
                                        @endif

                                        @if($referral->visit)
                                            <div class="mt-2 text-[12px] text-[#6B7280]">
                                                Kunjungan: 
                                                <a href="{{ route('visits.show', $referral->visit) }}" class="text-[#2563EB] hover:underline font-medium">
                                                    {{ $referral->visit->visit_number }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-right flex-shrink-0">
                                        <div class="text-[14px] font-semibold text-[#0F172A] mb-1">
                                            {{ optional($referral->scheduled_at)->translatedFormat('d/m/Y') ?? '—' }}
                                        </div>
                                        <div class="text-[13px] text-[#6B7280] mb-3">
                                            {{ optional($referral->scheduled_at)->translatedFormat('H:i') ?? '—' }}
                                        </div>
                                        <a href="{{ route('referrals.show', $referral) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-[6px] text-[13px] font-medium text-[#0F172A] hover:bg-[#EEF2FF] border border-[#E2E8F0] transition-colors whitespace-nowrap">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const labels = {
                    verifying: @js(__('Memeriksa data peserta BPJS...')),
                    participantFound: @js(__('Peserta ditemukan')),
                    name: @js(__('Nama')),
                    card: @js(__('No. Kartu')),
                    class: @js(__('Kelas')),
                    status: @js(__('Status')),
                    participantError: @js(__('Peserta tidak dapat diverifikasi.')),
                    syncSubmitting: @js(__('Mengirim permintaan sinkron...')),
                    syncQueued: @js(__('Permintaan sinkron berhasil diantrikan.')),
                    syncFailed: @js(__('Permintaan sinkron gagal dikirim.')),
                };

                const bpjsForm = document.getElementById('bpjs-verify-form');
                const bpjsButton = bpjsForm?.querySelector('.bpjs-verify');
                if (bpjsForm && bpjsButton) {
                    bpjsButton.addEventListener('click', async () => {
                        const statusTargetSelector = bpjsButton.dataset.target;
                        const statusTarget = statusTargetSelector ? document.querySelector(statusTargetSelector) : null;
                        const serviceDateInput = bpjsForm.querySelector('input[name="service_date"]');
                        const serviceDate = serviceDateInput?.value || new Date().toISOString().slice(0, 10);
                        const url = bpjsButton.dataset.url;
                        const nik = bpjsButton.dataset.nik;

                        if (!url || !nik || !statusTarget) {
                            return;
                        }

                        bpjsButton.disabled = true;
                        bpjsButton.classList.add('opacity-60');
                        statusTarget.textContent = labels.verifying;

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf ?? '',
                                },
                                body: JSON.stringify({ nik, service_date: serviceDate }),
                            });

                            const result = await response.json();

                            if (result.success) {
                                const peserta = result.data?.response?.peserta ?? {};
                                statusTarget.innerHTML = `
                                    <div class="mb-1 font-semibold text-slate-700">${labels.participantFound}</div>
                                    <div>${labels.name}: ${peserta.nama ?? '-'}</div>
                                    <div>${labels.card}: ${peserta.noKartu ?? '-'}</div>
                                    <div>${labels.class}: ${peserta.hakKelas?.keterangan ?? '-'}</div>
                                    <div>${labels.status}: ${peserta.statusPeserta?.keterangan ?? '-'}</div>
                                `;
                            } else {
                                statusTarget.innerHTML = `<span class="font-semibold text-rose-600">${result.message ?? labels.participantError}</span>`;
                            }
                        } catch (error) {
                            statusTarget.innerHTML = `<span class="font-semibold text-rose-600">${error.message ?? labels.participantError}</span>`;
                        } finally {
                            bpjsButton.disabled = false;
                            bpjsButton.classList.remove('opacity-60');
                        }
                    });
                }

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
                        target.innerHTML = labels.syncSubmitting;

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf ?? '',
                                },
                            });
                            const result = await response.json();

                            if (response.ok && result.success) {
                                target.innerHTML = `<span class="font-semibold text-emerald-700">${result.message ?? labels.syncQueued}</span>`;
                            } else {
                                target.innerHTML = `<span class="font-semibold text-rose-600">${result.message ?? labels.syncFailed}</span>`;
                            }
                        } catch (error) {
                            target.innerHTML = `<span class="font-semibold text-rose-600">${error.message ?? labels.syncFailed}</span>`;
                        } finally {
                            button.disabled = false;
                            button.classList.remove('opacity-60');
                        }
                    });
                });
            })();
        </script>
    @endpush
</x-app-layout>
