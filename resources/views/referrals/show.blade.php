<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-subtitle">{{ __('Referral #:number', ['number' => $referral->referral_number]) }}</p>
            <h1 class="page-title">{{ $referral->patient->name }}</h1>
        </div>
        <x-button :href="route('referrals.index')" variant="ghost">
            {{ __('Back to list') }}
        </x-button>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2">
            <div class="card-header">
                <h2 class="section-title">{{ __('Informasi Rujukan') }}</h2>
                <span class="badge badge-info">{{ __($referral->status) }}</span>
            </div>
            <div class="card-body space-y-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="stat-label">{{ __('Destination Facility') }}</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ $referral->referred_to }}</p>
                        <p class="text-sm text-slate-500">{{ $referral->referred_department ?: __('General') }}</p>
                    </div>
                    <div>
                        <p class="stat-label">{{ __('Scheduled') }}</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">
                            {{ optional($referral->scheduled_at)->format('d M Y H:i') ?? __('Not scheduled') }}
                        </p>
                    </div>
                    <div>
                        <p class="stat-label">{{ __('Contact Person') }}</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ $referral->contact_person ?: __('—') }}</p>
                        <p class="text-sm text-slate-500">{{ $referral->contact_phone ?: __('No phone') }}</p>
                    </div>
                    <div>
                        <p class="stat-label">{{ __('Related Visit') }}</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">
                            @if($referral->visit)
                                <a href="{{ route('visits.show', $referral->visit) }}" class="text-brand hover:underline">
                                    {{ $referral->visit->visit_number }}
                                </a>
                            @else
                                {{ __('Not linked') }}
                            @endif
                        </p>
                    </div>
                </div>

                <div>
                    <p class="stat-label">{{ __('Reason for Referral') }}</p>
                    <p class="mt-2 whitespace-pre-wrap text-sm text-slate-700">{{ $referral->reason }}</p>
                </div>

                @if($referral->notes)
                    <div>
                        <p class="stat-label">{{ __('Additional Notes') }}</p>
                        <p class="mt-2 whitespace-pre-wrap text-sm text-slate-700">{{ $referral->notes }}</p>
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="stat-label">{{ __('Created') }}</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $referral->created_at->format('d M Y H:i') }}</p>
                        <p class="text-xs text-slate-500">{{ __('By :name', ['name' => $referral->creator?->name ?? __('System')]) }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="stat-label">{{ __('Sent At') }}</p>
                        <p class="mt-2 text-sm text-slate-700">{{ optional($referral->sent_at)->format('d M Y H:i') ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="stat-label">{{ __('Responded At') }}</p>
                        <p class="mt-2 text-sm text-slate-700">{{ optional($referral->responded_at)->format('d M Y H:i') ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2 class="section-title">{{ __('Patient Snapshot') }}</h2>
            </div>
            <div class="card-body space-y-4 text-sm text-slate-700">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('MRN / NIK') }}</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $referral->patient->medical_record_number }}</p>
                    <p class="text-slate-500">{{ $referral->patient->nik }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Phone') }}</p>
                    <p class="mt-1">{{ $referral->patient->phone ?? __('Not provided') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Address') }}</p>
                    <p class="mt-1 whitespace-pre-wrap">{{ $referral->patient->address }}</p>
                </div>
            </div>
        </section>

        @can('referral.update')
            <section class="card lg:col-span-3">
                <div class="card-header">
                    <h2 class="section-title">{{ __('Update Status') }}</h2>
                </div>
                <form method="POST" action="{{ route('referrals.update', $referral) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <x-form.input
                                id="status"
                                name="status"
                                type="select"
                                label="{{ __('Status') }}"
                                :value="$referral->status">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(old('status', $referral->status) === $status)>{{ __($status) }}</option>
                                @endforeach
                            </x-form.input>

                            <x-form.input
                                id="responded_at"
                                name="responded_at"
                                type="datetime-local"
                                label="{{ __('Responded At') }}"
                                :value="old('responded_at', optional($referral->responded_at)->format('Y-m-d\TH:i'))"
                                help="{{ __('Set when the receiving facility confirms the referral.') }}" />
                        </div>

                        <x-form.input
                            id="notes"
                            name="notes"
                            type="textarea"
                            rows="3"
                            label="{{ __('Update Notes') }}"
                            :value="old('notes', $referral->notes)"
                            placeholder="{{ __('Add follow-up information or instructions.') }}" />
                    </div>
                    <div class="card-footer flex justify-end">
                        <x-button type="submit" icon="check-circle">
                            {{ __('Save Changes') }}
                        </x-button>
                    </div>
                </form>
            </section>
        @endcan
    </div>
</x-app-layout>
