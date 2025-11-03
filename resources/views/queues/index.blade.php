<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-[#64748B]">Pelayanan Pendaftaran</p>
                <h1 class="text-2xl font-bold text-[#0F172A]">Antrian Pasien</h1>
            </div>
            @can('visit.create')
                <a href="{{ route('visits.create') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#2563EB] bg-white px-4 py-2.5 text-sm font-semibold text-[#2563EB] hover:bg-[#F8FAFC]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Buat Kunjungan
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Filter Card -->
            <div class="mb-6 overflow-hidden rounded-2xl bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] px-6 py-4">
                    <h2 class="text-base font-semibold text-[#0F172A]">Filter Daftar Antrian</h2>
                    <p class="mt-1 text-xs text-[#64748B]">Pilih tanggal, status, atau cari nama/NIK/No. RM pasien.</p>
                </div>
                <div class="p-6">
                    <form method="GET" class="grid gap-4 md:grid-cols-6">
                        <div>
                            <label for="tanggal" class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1.5">TANGGAL</label>
                            <input type="date" id="tanggal" name="tanggal" value="{{ $filters['tanggal'] }}" class="block w-full h-10 rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1.5">STATUS</label>
                            <select id="status" name="status" class="block w-full h-10 rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" {{ ($filters['status'] ?? null) === $status ? 'selected' : '' }}>
                                        {{ ucfirst(strtolower($status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="search" class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1.5">CARI</label>
                            <input type="search"
                                   id="search"
                                   name="search"
                                   value="{{ $filters['search'] ?? '' }}"
                                   placeholder="Nama, No. RM, atau NIK"
                                   data-autocomplete="queues"
                                   data-autocomplete-url="{{ route('search.suggestions') }}"
                                   data-autocomplete-submit="true"
                                   class="block w-full h-10 rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end gap-2 md:col-span-2">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-[#2563EB] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1D4ED8]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Terapkan
                            </button>
                            <a href="{{ route('queues.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @can('queue.create')
            <!-- Add Queue Form -->
            <div class="mb-6 overflow-hidden rounded-2xl border border-[#E2E8F0] bg-[#F8FAFC] shadow-sm">
                <div class="px-6 py-4">
                    <h3 class="text-sm font-semibold text-[#0F172A]">Tambah Nomor Antrian</h3>
                </div>
                <div class="px-6 pb-6">
                    <form method="POST" action="{{ route('queues.store') }}" class="grid gap-4 md:grid-cols-6">
                        @csrf
                        <div class="md:col-span-2">
                            <label for="patient_id" class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1.5">PASIEN *</label>
                            <select id="patient_id" name="patient_id" required class="block w-full h-10 rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih pasien...</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->medical_record_number }} - {{ $patient->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="queue_department" class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1.5">POLI *</label>
                            <select id="queue_department" name="department" required class="block w-full h-10 rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih poli...</option>
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
                            <label for="tanggal_antrian" class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1.5">TANGGAL *</label>
                            <input type="date" id="tanggal_antrian" name="tanggal_antrian" value="{{ $filters['tanggal'] }}" required class="block w-full h-10 rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="nomor_antrian" class="block text-xs font-semibold uppercase tracking-wider text-[#64748B] mb-1.5">NOMOR</label>
                            <input type="text"
                                   id="nomor_antrian"
                                   name="nomor_antrian"
                                   value="{{ old('nomor_antrian', $queueNumberMap[$selectedDepartment] ?? '') }}"
                                   class="block w-full h-10 rounded-xl border-gray-300 text-sm uppercase bg-[#F8FAFC]"
                                   readonly
                                   data-default="{{ $queueNumberMap[$selectedDepartment] ?? '' }}">
                        </div>
                        <div class="flex items-end">
                            <input type="hidden" name="redirect_to" value="{{ request()->routeIs('registrations.index') ? 'registrations.index' : '' }}">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#2563EB] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#1D4ED8]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Tambahkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endcan

            <!-- Queue Table -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#E2E8F0]">
                        <thead class="bg-[#F8FAFC]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Nomor</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Pasien</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Kunjungan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0] bg-white">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-[#F8FAFC]">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-lg font-bold text-[#0F172A]">{{ $ticket->nomor_antrian }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm font-medium text-[#0F172A]">{{ $ticket->patient->name ?? '-' }}</p>
                                            <p class="text-xs text-[#64748B]">RM {{ $ticket->patient->medical_record_number ?? '-' }}</p>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'DIPANGGIL' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
                                                'MENUNGGU' => ['bg' => '#FEF3C7', 'text' => '#F59E0B'],
                                                'BATAL' => ['bg' => '#FEE2E2', 'text' => '#DC2626'],
                                                'SELESAI' => ['bg' => '#D1FAE5', 'text' => '#16A34A'],
                                            ];
                                            $color = $statusColors[$ticket->status] ?? ['bg' => '#E2E8F0', 'text' => '#64748B'];
                                        @endphp
                                        <span class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold" style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($ticket->visit)
                                            <a href="{{ route('visits.show', $ticket->visit) }}" class="text-sm font-medium text-[#2563EB] hover:text-[#1D4ED8]">
                                                {{ $ticket->visit->kode_kunjungan }}
                                            </a>
                                        @else
                                            <span class="text-xs text-[#94A3B8]">-</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if($ticket->status === 'MENUNGGU')
                                                @can('queue.update')
                                                <form method="POST" action="{{ route('queues.update', $ticket) }}" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="DIPANGGIL">
                                                    <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-[#2563EB] bg-white px-3 py-1.5 text-xs font-semibold text-[#2563EB] hover:bg-[#F8FAFC]">
                                                        Dipanggil
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif

                                            @if(in_array($ticket->status, ['MENUNGGU', 'DIPANGGIL']))
                                                @can('visit.create')
                                                <a href="{{ route('visits.create', ['queue_id' => $ticket->id]) }}" class="inline-flex items-center justify-center rounded-lg bg-[#2563EB] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#1D4ED8]">
                                                    Simpan
                                                </a>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-[#64748B]">
                                        Tidak ada data antrian untuk tanggal ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tickets->hasPages())
                <div class="border-t border-[#E2E8F0] px-6 py-4">
                    {{ $tickets->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const queueNumberMap = @json($queueNumberMap ?? []);
            const departmentSelect = document.getElementById('queue_department');
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
        });
    </script>
@endpush
