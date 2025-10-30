@props(['visit'])

<section class="card">
    <div class="card-header">
        <div>
            <p class="page-subtitle">{{ __('Catatan Medis') }}</p>
            <h2 class="section-title">{{ __('Formulir SOAP') }}</h2>
        </div>
        <span class="badge badge-neutral">
            {{ $visit->emrNotes->count() }} {{ __('catatan') }}
        </span>
    </div>

    <div class="card-body space-y-6">
        @can('emr.create')
            <form method="POST" action="{{ route('emr.store', $visit) }}" class="space-y-4">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="subjective" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Subjective (S)') }}
                        </label>
                        <textarea id="subjective" name="subjective" rows="3" class="textarea" placeholder="{{ __('Keluhan utama pasien') }}">{{ old('subjective') }}</textarea>
                        <x-input-error :messages="$errors->get('subjective')" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="objective" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Objective (O)') }}
                        </label>
                        <textarea id="objective" name="objective" rows="3" class="textarea" placeholder="{{ __('Pemeriksaan fisik, tanda vital, laboratorium') }}">{{ old('objective') }}</textarea>
                        <x-input-error :messages="$errors->get('objective')" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="assessment" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Assessment (A)') }}
                        </label>
                        <textarea id="assessment" name="assessment" rows="3" class="textarea" placeholder="{{ __('Diagnosis kerja / banding') }}">{{ old('assessment') }}</textarea>
                        <x-input-error :messages="$errors->get('assessment')" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="plan" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Plan (P)') }}
                        </label>
                        <textarea id="plan" name="plan" rows="3" class="textarea" placeholder="{{ __('Rencana terapi / tindak lanjut') }}">{{ old('plan') }}</textarea>
                        <x-input-error :messages="$errors->get('plan')" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="icd10_code" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Kode ICD-10') }}
                        </label>
                        <input id="icd10_code"
                               name="icd10_code"
                               type="text"
                               value="{{ old('icd10_code') }}"
                               maxlength="10"
                               class="input uppercase"
                               placeholder="A09">
                        <x-input-error :messages="$errors->get('icd10_code')" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="icd10_description" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ __('Deskripsi ICD-10') }}
                        </label>
                        <input id="icd10_description"
                               name="icd10_description"
                               type="text"
                               value="{{ old('icd10_description') }}"
                               class="input"
                               placeholder="{{ __('Contoh: Gastroenteritis akut') }}">
                        <x-input-error :messages="$errors->get('icd10_description')" />
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="notes" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        {{ __('Catatan Tambahan') }}
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="3"
                              class="textarea"
                              placeholder="{{ __('Instruksi tambahan, edukasi pasien, atau observasi lainnya') }}">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" />
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-button type="submit" icon="check-circle">
                        {{ __('Simpan Catatan EMR') }}
                    </x-button>
                </div>
            </form>
        @endcan

        <div class="space-y-4">
            @forelse ($visit->emrNotes as $note)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                    <header class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                        <span>{{ $note->author?->name ?? __('Petugas tidak diketahui') }}</span>
                        <span>{{ $note->created_at->translatedFormat('d F Y H:i') }}</span>
                    </header>
                    <dl class="mt-3 grid gap-3 text-sm text-slate-700">
                        @foreach (['subjective' => 'Subjective', 'objective' => 'Objective', 'assessment' => 'Assessment', 'plan' => 'Plan'] as $field => $label)
                            @if ($note->{$field})
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                                    <dd class="mt-1 leading-relaxed text-slate-700">{{ $note->{$field} }}</dd>
                                </div>
                            @endif
                        @endforeach
                        @if ($note->icd10_code)
                            <div class="rounded-lg bg-white px-4 py-2 text-xs text-slate-600">
                                {{ __('Diagnosis: :code - :desc', ['code' => $note->icd10_code, 'desc' => $note->icd10_description]) }}
                            </div>
                        @endif
                        @if ($note->notes)
                            <div class="rounded-lg bg-white px-4 py-2 text-xs text-slate-600">
                                {{ $note->notes }}
                            </div>
                        @endif
                    </dl>
                </article>
            @empty
                <x-empty
                    title="{{ __('Belum ada catatan EMR') }}"
                    message="{{ __('Isi formulir untuk menambahkan catatan klinis pertama.') }}"
                    icon="clipboard-document-list" />
            @endforelse
        </div>
    </div>
</section>
