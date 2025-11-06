<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-subtitle">Penjadwalan Kunjungan</p>
            <h1 class="page-title">Buat Kunjungan Baru</h1>
        </div>
        <x-button :href="route('visits.index')" variant="ghost" icon="arrow-left">
            {{ __('Kembali ke daftar') }}
        </x-button>
    </x-slot>

    <section class="card">
        <form method="POST" action="{{ route('visits.store') }}">
            @csrf
            <div class="card-body space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="patient_id" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Pilih Pasien') }} <span class="text-danger">*</span>
                        </label>
                        <select id="patient_id" name="patient_id" class="select" required>
                            <option value="">{{ __('Pilih pasien') }}</option>
                            @foreach ($patients as $option)
                                <option value="{{ $option->id }}" @selected(old('patient_id', $patient?->id) == $option->id)>
                                    {{ $option->medical_record_number }} - {{ $option->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('patient_id')" class="mt-1" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="provider_id" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Dokter Penanggung Jawab') }}
                        </label>
                        <select id="provider_id" name="provider_id" class="select">
                            <option value="">{{ __('Tentukan nanti') }}</option>
                            @foreach ($providers as $provider)
                                <option value="{{ $provider->id }}" @selected(old('provider_id') == $provider->id)>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('provider_id')" class="mt-1" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="visit_datetime" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Tanggal & Jam Kunjungan') }} <span class="text-danger">*</span>
                        </label>
                        <input id="visit_datetime"
                               type="datetime-local"
                               name="visit_datetime"
                               value="{{ old('visit_datetime', now()->format('Y-m-d\TH:i')) }}"
                               class="input"
                               required>
                        <x-input-error :messages="$errors->get('visit_datetime')" class="mt-1" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="clinic_name" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Poli / Unit Pelayanan') }} <span class="text-danger">*</span>
                        </label>
                        <select id="clinic_name" name="clinic_name" class="select" required>
                            @foreach ($poliOptions as $poli)
                                <option value="{{ $poli }}" @selected(old('clinic_name', $defaultClinic) == $poli)>
                                    {{ $poli }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('clinic_name')" class="mt-1" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                    <div class="flex flex-col gap-2">
                        <label for="queue_number" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Nomor Antrian (Otomatis)') }}
                        </label>
                        <input id="queue_number"
                               type="text"
                               name="queue_number"
                               value="{{ old('queue_number') }}"
                               class="input uppercase bg-slate-50"
                               placeholder="{{ $nextQueueNumber }}"
                               readonly>
                        <p class="mt-1 text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                                Nomor antrian otomatis sesuai poli yang dipilih
                            </span>
                        </p>
                        <x-input-error :messages="$errors->get('queue_number')" class="mt-1" />
                    </div>
                    <button type="button"
                            id="queue-preview"
                            class="btn btn-ghost w-full md:w-auto border border-slate-300"
                            disabled>
                        <x-icon.queue-list class="h-5 w-5" />
                        <span id="queue-preview-text">{{ $nextQueueNumber }}</span>
                    </button>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="coverage_type" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Jenis Pembiayaan') }}
                        </label>
                        <select id="coverage_type" name="coverage_type" class="select">
                            <option value="BPJS" @selected(old('coverage_type') === 'BPJS')>BPJS</option>
                            <option value="UMUM" @selected(old('coverage_type', 'UMUM') === 'UMUM')>Umum</option>
                        </select>
                        <x-input-error :messages="$errors->get('coverage_type')" class="mt-1" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <label for="sep_no" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                {{ __('Nomor SEP (BPJS)') }}
                            </label>
                            <input id="sep_no"
                                   type="text"
                                   name="sep_no"
                                   value="{{ old('sep_no') }}"
                                   class="input"
                                   placeholder="SEP123456789">
                            <x-input-error :messages="$errors->get('sep_no')" class="mt-1" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="bpjs_reference_no" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                {{ __('Nomor Rujukan BPJS') }}
                            </label>
                            <input id="bpjs_reference_no"
                                   type="text"
                                   name="bpjs_reference_no"
                                   value="{{ old('bpjs_reference_no') }}"
                                   class="input"
                                   placeholder="REF123456789">
                            <x-input-error :messages="$errors->get('bpjs_reference_no')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="chief_complaint" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Keluhan Utama') }}
                        </label>
                        <textarea id="chief_complaint"
                                  name="chief_complaint"
                                  rows="4"
                                  class="textarea"
                                  placeholder="{{ __('Ringkas keluhan utama pasien') }}">{{ old('chief_complaint') }}</textarea>
                        <x-input-error :messages="$errors->get('chief_complaint')" class="mt-1" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="triage_notes" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Catatan Triage') }}
                        </label>
                        <textarea id="triage_notes"
                                  name="triage_notes"
                                  rows="4"
                                  class="textarea"
                                  placeholder="{{ __('Catatan triase, tanda vital, dsb.') }}">{{ old('triage_notes') }}</textarea>
                        <x-input-error :messages="$errors->get('triage_notes')" class="mt-1" />
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <x-button :href="route('visits.index')" type="button" variant="ghost">
                    {{ __('Batal') }}
                </x-button>
                <x-button type="submit" icon="check-circle">
                    {{ __('Simpan Kunjungan') }}
                </x-button>
            </div>
        </form>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const clinicSelect = document.getElementById('clinic_name');
                const queueInput = document.getElementById('queue_number');
                const queuePreviewText = document.getElementById('queue-preview-text');

                // Queue number map dari backend
                const queueNumberMap = @json($queueNumberMap);

                // Update queue number preview saat poli berubah
                if (clinicSelect && queueInput && queuePreviewText) {
                    clinicSelect.addEventListener('change', () => {
                        const selectedClinic = clinicSelect.value;
                        const nextNumber = queueNumberMap[selectedClinic] || 'A001';

                        // Update placeholder dan preview
                        queueInput.placeholder = nextNumber;
                        queuePreviewText.textContent = nextNumber;

                        // Clear input value agar otomatis generate
                        queueInput.value = '';

                        console.log(`Poli: ${selectedClinic} → Next Queue: ${nextNumber}`);
                    });

                    // Set initial preview
                    const initialClinic = clinicSelect.value;
                    const initialQueue = queueNumberMap[initialClinic] || queueInput.placeholder;
                    queuePreviewText.textContent = initialQueue;
                }
            });
        </script>
    @endpush
</x-app-layout>
