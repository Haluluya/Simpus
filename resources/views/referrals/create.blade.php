<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-subtitle">{{ __('New Referral') }}</p>
            <h1 class="page-title">{{ __('Buat Rujukan') }}</h1>
        </div>
        <x-button :href="route('referrals.index')" variant="ghost">
            {{ __('Back to list') }}
        </x-button>
    </x-slot>

    <section class="card">
        <form method="POST" action="{{ route('referrals.store') }}">
            @csrf
            <div class="card-body space-y-8">
                <div class="form-grid">
                    <x-form.input
                        id="patient_id"
                        name="patient_id"
                        type="select"
                        label="{{ __('Patient') }}"
                        required>
                        <option value="">{{ __('Select patient') }}</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(old('patient_id', $selectedPatientId) == $patient->id)>
                                {{ $patient->medical_record_number }} — {{ $patient->name }}
                            </option>
                        @endforeach
                    </x-form.input>

                    <x-form.input
                        id="visit_id"
                        name="visit_id"
                        type="select"
                        label="{{ __('Related Visit (optional)') }}">
                        <option value="">{{ __('Select visit') }}</option>
                        @foreach ($visits as $visit)
                            <option value="{{ $visit->id }}" @selected(old('visit_id', $selectedVisitId) == $visit->id)>
                                {{ $visit->visit_number }} — {{ $visit->patient->name }} ({{ optional($visit->visit_datetime)->format('d M Y H:i') }})
                            </option>
                        @endforeach
                    </x-form.input>

                    <x-form.input
                        id="referred_to"
                        name="referred_to"
                        label="{{ __('Destination Facility') }}"
                        :value="old('referred_to')"
                        required />

                    <x-form.input
                        id="referred_department"
                        name="referred_department"
                        label="{{ __('Department / Unit') }}"
                        :value="old('referred_department')"
                        placeholder="{{ __('e.g., Cardiologist, Surgery') }}" />

                    <x-form.input
                        id="contact_person"
                        name="contact_person"
                        label="{{ __('Contact Person') }}"
                        :value="old('contact_person')"
                        placeholder="{{ __('Name of receiving staff') }}" />

                    <x-form.input
                        id="contact_phone"
                        name="contact_phone"
                        label="{{ __('Contact Phone') }}"
                        :value="old('contact_phone')"
                        placeholder="08xxxxxxxxxx" />

                    <x-form.input
                        id="scheduled_at"
                        name="scheduled_at"
                        type="datetime-local"
                        label="{{ __('Scheduled Date/Time') }}"
                        :value="old('scheduled_at')" />

                    <x-form.input
                        id="status"
                        name="status"
                        type="select"
                        label="{{ __('Status') }}">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', 'PENDING') === $status)>{{ __($status) }}</option>
                        @endforeach
                    </x-form.input>
                </div>

                <div class="form-grid">
                    <x-form.input
                        id="reason"
                        name="reason"
                        type="textarea"
                        rows="3"
                        label="{{ __('Reason for Referral') }}"
                        :value="old('reason')"
                        required />

                    <x-form.input
                        id="notes"
                        name="notes"
                        type="textarea"
                        rows="3"
                        label="{{ __('Additional Notes') }}"
                        :value="old('notes')"
                        placeholder="{{ __('Transportation, medication, or other instructions') }}" />
                </div>
            </div>

            <div class="card-footer flex justify-end gap-3">
                <x-button :href="route('referrals.index')" type="button" variant="ghost">
                    {{ __('Cancel') }}
                </x-button>
                <x-button type="submit" icon="paper-airplane">
                    {{ __('Save Referral') }}
                </x-button>
            </div>
        </form>
    </section>
</x-app-layout>
