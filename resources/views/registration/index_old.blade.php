<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="page-title">{{ __('Pendaftaran & Antrian') }}</h1>
                <p class="mt-1 text-[13px] text-[#94A3B8]">Kelola pendaftaran pasien dan antrian kunjungan</p>
            </div>
            <x-button :href="route('patients.create')" icon="user-plus" class="!h-[52px]">
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
        <article class="card">
            <div class="card-header">
                <h2 class="section-title">Registrasi Pasien Baru</h2>
                <span class="badge badge-info">Auto Antrian: {{ $nextQueueNumber }}</span>
            </div>
            <form method="POST"
                  action="{{ route('patients.store') }}"
                  class="card-body space-y-5">
                @csrf
                <input type="hidden" name="redirect_to" value="registrations.index">
                <input type="hidden" name="queue_date" value="{{ $selectedDate }}">
                <label class="inline-flex items-center gap-2 text-[14px] font-medium text-[#475569]">
                    <input type="checkbox" name="enqueue_after" value="1" class="rounded border-[#E2E8F0] text-[#2563EB] focus:ring-[#2563EB]/40" checked>
                    Otomatis buat nomor antrian setelah simpan pasien
                </label>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="name" class="text-[14px] font-semibold text-[#0F172A]">Nama Lengkap<span class="text-[#DC2626]">*</span></label>
                        <input id="name" name="name" type="text" class="input mt-1.5" required value="{{ old('name') }}">
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <label for="nik" class="text-[14px] font-semibold text-[#0F172A]">NIK<span class="text-[#DC2626]">*</span></label>
                        <input id="nik" name="nik" type="text" class="input mt-1.5" maxlength="20" required value="{{ old('nik') }}">
                        <x-input-error :messages="$errors->get('nik')" class="mt-1" />
                    </div>
                    <div>
                        <label for="bpjs_card_no" class="text-[14px] font-semibold text-[#0F172A]">No. Kartu BPJS</label>
                        <input id="bpjs_card_no" name="bpjs_card_no" type="text" class="input mt-1.5" maxlength="30" value="{{ old('bpjs_card_no') }}">
                        <x-input-error :messages="$errors->get('bpjs_card_no')" class="mt-1" />
                    </div>
                    <div>
                        <label for="date_of_birth" class="text-[14px] font-semibold text-[#0F172A]">Tanggal Lahir<span class="text-[#DC2626]">*</span></label>
                        <input id="date_of_birth" name="date_of_birth" type="date" class="input mt-1.5" required value="{{ old('date_of_birth') }}">
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1" />
                    </div>
                    <div>
                        <label for="gender" class="text-[14px] font-semibold text-[#0F172A]">Jenis Kelamin<span class="text-[#DC2626]">*</span></label>
                        <select id="gender" name="gender" class="input mt-1.5">
                            <option value="">Pilih</option>
                            <option value="male" @selected(old('gender') === 'male')>Laki-laki</option>
                            <option value="female" @selected(old('gender') === 'female')>Perempuan</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-1" />
                    </div>
                    <div>
                        <label for="phone" class="text-[14px] font-semibold text-[#0F172A]">No. Telepon</label>
                        <input id="phone" name="phone" type="text" class="input mt-1.5" value="{{ old('phone') }}">
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="text-[14px] font-semibold text-[#0F172A]">Alamat</label>
                        <textarea id="address" name="address" rows="3" class="input mt-1.5">{{ old('address') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-1" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <x-button type="reset" variant="outline" class="!h-[44px]">Atur Ulang</x-button>
                    <x-button type="submit" icon="check-circle" class="!h-[44px]">Simpan & Ambil Antrian</x-button>
                </div>
            </form>
        </article>

        <article class="card">
            <div class="card-header">
                <h2 class="section-title">Pasien Terdaftar</h2>
                <form method="GET" class="w-full sm:w-auto">
                    <input type="hidden" name="tanggal" value="{{ $selectedDate }}">
                    <label for="search" class="sr-only">Cari pasien</label>
                    <div class="flex items-center overflow-hidden rounded-[12px] border border-[#E2E8F0] bg-white shadow-sm">
                        <input
                            id="search"
                            name="search"
                            type="search"
                            placeholder="Cari nama, NIK, atau No. RM"
                            value="{{ $search }}"
                            class="h-[44px] min-w-[240px] flex-1 border-0 px-4 text-[14px] placeholder-[#94A3B8] focus:ring-0 focus:outline-none"
                        >
                        <button type="submit" class="inline-flex h-[44px] items-center gap-2 bg-[#2563EB] px-4 font-semibold text-white hover:bg-[#1D4ED8] focus:outline-none">
                            <x-icon.magnifying-glass class="h-5 w-5" />
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="card-body space-y-5">
                <form method="POST" action="{{ route('registrations.queue.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="tanggal_antrian" value="{{ $selectedDate }}">

                    <div>
                        <label for="patient_id" class="text-sm font-semibold text-slate-700">Pilih Pasien</label>
                        <select id="patient_id" name="patient_id" class="input mt-1" required>
                            <option value="">-- Pilih pasien --</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)">
                                    {{ $patient->name }} • RM {{ $patient->medical_record_number }} • NIK {{ $patient->nik }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('patient_id')" class="mt-1" />
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label for="nomor_antrian" class="text-sm font-semibold text-slate-700">Nomor Antrian</label>
                            <input id="nomor_antrian" name="nomor_antrian" type="text" class="input mt-1 uppercase" placeholder="{{ $nextQueueNumber }}">
                            <p class="mt-1 text-xs text-slate-500">Kosongkan untuk otomatis.</p>
                        </div>
                        <div class="flex items-end">
                            <x-button type="submit" class="w-full" icon="queue-list">Tambah ke Antrian</x-button>
                        </div>
                    </div>
                </form>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="section-title mb-3">Riwayat Singkat</h3>
                    <ul class="space-y-3">
                        @forelse ($queueTickets as $ticket)
                            <li class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $ticket->patient->name }}</p>
                                        <p class="text-xs text-slate-500">RM {{ $ticket->patient->medical_record_number }} • NIK {{ $ticket->patient->nik }}</p>
                                    </div>
                                    <span class="badge badge-info">{{ $ticket->nomor_antrian }}</span>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Status: {{ \Illuminate\Support\Str::headline(strtolower($ticket->status)) }}</p>
                            </li>
                        @empty
                            <x-empty
                                title="Belum ada antrian"
                                message="Nomor antrian akan muncul setelah pendaftaran berhasil."
                                icon="queue-list" />
                        @endforelse
                    </ul>
                </div>
            </div>
        </article>
    </section>

    <section class="card">
        <div class="card-header">
            <h2 class="section-title">Antrian Hari Ini</h2>
        </div>
        <div class="card-body">
            @if ($queueTickets->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">NOMOR</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">PASIEN</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">STATUS</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-600">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($queueTickets as $ticket)
                                <tr class="border-b border-slate-100 transition-colors hover:bg-slate-50">
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $ticket->nomor_antrian }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-900">{{ $ticket->patient->name }}</div>
                                        <p class="text-xs text-slate-500">RM {{ $ticket->patient->medical_record_number }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusClass = [
                                                'MENUNGGU' => 'bg-blue-100 text-blue-800',
                                                'DIPANGGIL' => 'bg-amber-100 text-amber-800',
                                                'SELESAI' => 'bg-emerald-100 text-emerald-800',
                                                'BATAL' => 'bg-red-100 text-red-800',
                                            ][$ticket->status] ?? 'bg-slate-100 text-slate-800';
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $statusClass }}">
                                            {{ \Illuminate\Support\Str::headline(strtolower($ticket->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('queues.update', $ticket) }}" class="flex items-center justify-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="redirect_to" value="registrations.index">
                                            <select name="status" class="h-10 rounded border border-slate-300 px-2 text-sm">
                                                @foreach (\App\Models\QueueTicket::statuses() as $status)
                                                    <option value="{{ $status }}" @selected($status === $ticket->status)>{{ \Illuminate\Support\Str::headline(strtolower($status)) }}</option>
                                                @endforeach
                                            </select>
                                            <x-button type="submit" variant="outline">Perbarui</x-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center">
                    <p class="text-slate-500">Belum ada antrian untuk hari ini.</p>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
