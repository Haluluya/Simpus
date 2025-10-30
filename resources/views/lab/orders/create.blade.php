<x-app-layout>
    <div class="min-h-screen bg-[#F1F5F9] px-6 py-8">
        <div class="mx-auto max-w-6xl">
            {{-- Header --}}
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#475569]">Create Lab Order</h1>
                    @if ($visit)
                        <p class="mt-1 text-sm text-[#64748B]">
                            {{ $visit->patient->name }} · {{ optional($visit->visit_datetime)->format('d M Y H:i') }}
                        </p>
                    @else
                        <p class="mt-1 text-sm text-[#64748B]">
                            Select a visit to proceed.
                        </p>
                    @endif
                </div>
                <a href="{{ route('visits.index') }}" class="inline-flex items-center rounded-lg border border-[#CBD5E1] bg-white px-4 py-2.5 text-sm font-semibold text-[#64748B] hover:bg-[#F8FAFC]">
                    Back to visits
                </a>
            </div>

            <form method="POST" action="{{ route('lab-orders.store') }}" id="lab-order-form" class="space-y-6">
                @csrf

                <div class="rounded-2xl bg-white p-8 shadow-sm">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="visit_id" class="mb-2 block text-sm font-semibold text-[#475569]">Kunjungan</label>
                            <select id="visit_id" name="visit_id" class="block h-12 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20" required>
                                <option value="">Select visit</option>
                                @foreach ($recentVisits as $recent)
                                    <option value="{{ $recent->id }}" @selected(old('visit_id', $visit?->id) == $recent->id)>
                                        {{ $recent->patient->name }} · {{ optional($recent->visit_datetime)->format('d M Y H:i') }} · {{ $recent->clinic_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('visit_id')" class="mt-2" />
                        </div>

                        <div>
                            <label for="priority" class="mb-2 block text-sm font-semibold text-[#475569]">Priority</label>
                            <select id="priority" name="priority" class="block h-12 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">
                                <option value="ROUTINE" @selected(old('priority') === 'ROUTINE')>Routine</option>
                                <option value="STAT" @selected(old('priority') === 'STAT')>STAT (Urgent)</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>

                        <div class="sm:col-span-2">
                            <label for="clinical_notes" class="mb-2 block text-sm font-semibold text-[#475569]">Clinical Notes</label>
                            <textarea id="clinical_notes" name="clinical_notes" rows="4" class="block w-full rounded-xl border border-[#CBD5E1] px-4 py-3 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20">{{ old('clinical_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('clinical_notes')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-8 shadow-sm">
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-bold text-[#475569]">Lab Tests</h3>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" id="add-test-row" class="inline-flex items-center rounded-lg border border-[#CBD5E1] bg-white px-4 py-2 text-sm font-semibold text-[#64748B] hover:bg-[#F8FAFC]">
                                Add Test Row
                            </button>
                            @foreach ($defaultPanels as $panel)
                                <button type="button" class="panel-quick-fill inline-flex items-center rounded-lg border border-[#3B82F6] bg-[#EFF6FF] px-4 py-2 text-sm font-semibold text-[#3B82F6] hover:bg-[#DBEAFE]" data-test="{{ $panel }}">
                                    {{ $panel }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-[#E2E8F0]">
                        <table class="min-w-full" id="lab-tests-table">
                            <thead class="bg-[#F8FAFC]">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">NAMA PEMERIKSAAN</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">LOINC CODE</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">SPECIMEN</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-[#64748B]">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0] bg-white" id="lab-tests-body">
                                @php
                                    $oldTests = old('tests', [['test_name' => '', 'loinc_code' => '', 'specimen_type' => '']]);
                                @endphp
                                @foreach ($oldTests as $index => $test)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <input type="text" name="tests[{{ $index }}][test_name]" class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20" required value="{{ $test['test_name'] ?? '' }}">
                                            <x-input-error :messages="$errors->get("tests.$index.test_name")" class="mt-1" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="tests[{{ $index }}][loinc_code]" class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm uppercase text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20" value="{{ $test['loinc_code'] ?? '' }}">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="tests[{{ $index }}][specimen_type]" class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20" value="{{ $test['specimen_type'] ?? '' }}">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" class="remove-test-row inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-semibold text-[#DC2626] hover:bg-[#FEE2E2]">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('visits.index') }}" class="inline-flex h-12 items-center rounded-xl border border-[#CBD5E1] bg-white px-6 text-sm font-semibold text-[#64748B] hover:bg-[#F8FAFC]">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex h-12 items-center rounded-xl bg-[#16A34A] px-6 text-sm font-semibold text-white hover:bg-[#15803D]">
                        Save Lab Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {});

            const testsBody = document.querySelector('#lab-tests-body');
            const addTestButton = document.querySelector('#add-test-row');
            let testIndex = {{ count($oldTests) }};

            const createRow = (data = {}) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <input type="text" name="tests[${testIndex}][test_name]" class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20" required value="${data.test_name || ''}">
                    </td>
                    <td class="px-4 py-3">
                        <input type="text" name="tests[${testIndex}][loinc_code]" class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm uppercase text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20" value="${data.loinc_code || ''}">
                    </td>
                    <td class="px-4 py-3">
                        <input type="text" name="tests[${testIndex}][specimen_type]" class="h-10 w-full rounded-lg border border-[#CBD5E1] px-3 text-sm text-[#0F172A] focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20" value="${data.specimen_type || ''}">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="remove-test-row inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-semibold text-[#DC2626] hover:bg-[#FEE2E2]">
                            Hapus
                        </button>
                    </td>
                `;
                testsBody.appendChild(row);
                testIndex++;
            };

            addTestButton?.addEventListener('click', () => createRow());

            testsBody?.addEventListener('click', (event) => {
                if (event.target.matches('.remove-test-row')) {
                    const rows = testsBody.querySelectorAll('tr');
                    if (rows.length > 1) {
                        event.target.closest('tr').remove();
                    }
                }
            });

            document.querySelectorAll('.panel-quick-fill').forEach((button) => {
                button.addEventListener('click', () => {
                    createRow({ test_name: button.dataset.test });
                });
            });
        </script>
    @endpush
</x-app-layout>
