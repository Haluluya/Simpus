<x-app-layout>
    @php $isDoctor = ($isDoctor ?? (auth()->user()?->hasRole('doctor') ?? false)); @endphp
    <div class="min-h-screen bg-[#F8FAFC] px-6 py-8">
        <div class="mx-auto max-w-7xl">
            @if ($isDoctor)
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-[#1E293B]">Antrean Pasien Poli Saya</h1>
                    <p class="mt-1 text-sm text-[#64748B]">Fokus pelayanan: panggil pasien dari antrean dan buka RME</p>
                </div>

                <section class="rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                    <div class="border-b border-[#E2E8F0] px-6 py-4">
                        <h2 class="text-lg font-semibold text-[#1E293B]">Daftar Antrean Hari Ini</h2>
                    </div>
                    <div class="overflow-x-auto p-6">
                        <table class="w-full table-auto text-left">
                            <thead>
                                <tr class="text-xs uppercase tracking-wide text-[#64748B]">
                                    <th class="pb-2 pr-4">No. Antrean</th>
                                    <th class="pb-2 pr-4">Nama Pasien</th>
                                    <th class="pb-2 pr-4">Status</th>
                                    <th class="pb-2 pr-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0]">
                                @forelse(($doctorQueues ?? collect()) as $row)
                                    <tr class="align-middle">
                                        <td class="py-3 pr-4 font-semibold text-[#0F172A]">{{ $row->nomor_antrian }}</td>
                                        <td class="py-3 pr-4">
                                            <div class="text-sm font-medium text-[#0F172A]">{{ $row->patient?->name }}</div>
                                            <div class="text-xs text-[#64748B]">{{ $row->patient?->medical_record_number }}</div>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center rounded-lg bg-[#DBEAFE] px-2.5 py-1 text-xs font-semibold text-[#2563EB]">{{ ucfirst(strtolower($row->status)) }}</span>
                                        </td>
                                        <td class="py-3 pr-4 text-right">
                                            <form method="POST" action="{{ route('queues.serve', $row) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">
                                                    <x-icon.queue-list class="h-5 w-5" />
                                                    <span>Layani</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-sm text-[#64748B]">
                                            Belum ada antrean menunggu untuk poli Anda hari ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- Tidak menampilkan grafik atau widget lain untuk Dokter --}}
            @else
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-[#1E293B]">Dasbor SIMPUS</h1>
                <p class="mt-1 text-sm text-[#64748B]">Selamat datang di Sistem Informasi Manajemen Puskesmas</p>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-[#64748B]">Kunjungan Hari Ini</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ $metrics['visits_today'] ?? 0 }}</p>
                            @php
                                $percentToday = $metrics['visits_today'] > 0 ? '+12%' : '0%';
                            @endphp
                            <p class="mt-1 text-xs text-[#16A34A]">{{ $percentToday }} dari kemarin</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DBEAFE]">
                            <svg class="h-6 w-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-[#64748B]">Kunjungan Bulan Ini</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ number_format($metrics['visits_this_month'] ?? 0) }}</p>
                            <p class="mt-1 text-xs text-[#16A34A]">+8% dari bulan lalu</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DBEAFE]">
                            <svg class="h-6 w-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-[#64748B]">Total Pasien Terdaftar</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ number_format($metrics['total_patients'] ?? 0) }}</p>
                            <p class="mt-1 text-xs text-[#16A34A]">+156 pasien baru</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DBEAFE]">
                            <svg class="h-6 w-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-[#64748B]">Antrian Menunggu</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ $metrics['queue_waiting'] ?? 0 }}</p>
                            <p class="mt-1 text-xs text-[#64748B]">Pasien menunggu</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DBEAFE]">
                            <svg class="h-6 w-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-[#1E293B]">Tren Kunjungan 7 Hari Terakhir</h2>
                    <div class="h-64">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-[#1E293B]">Kunjungan Berdasarkan Pembiayaan</h2>
                    <div class="h-64">
                        <canvas id="coverageChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="mb-6 rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-[#1E293B]">Aksi Cepat</h2>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
                    @can('patient.create')
                    <a href="{{ route('patients.create') }}" class="flex flex-col items-center justify-center rounded-xl border border-[#E2E8F0] bg-white p-6 text-center transition hover:border-[#2563EB] hover:bg-[#F8FAFC]">
                        <svg class="h-8 w-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-[#1E293B]">Tambah Pasien</p>
                    </a>
                    @endcan

                    @can('visit.create')
                    <a href="{{ route('visits.create') }}" class="flex flex-col items-center justify-center rounded-xl border border-[#E2E8F0] bg-white p-6 text-center transition hover:border-[#2563EB] hover:bg-[#F8FAFC]">
                        <svg class="h-8 w-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-[#1E293B]">Catat Kunjungan</p>
                    </a>
                    @endcan

                    @can('queue.view')
                    <a href="{{ route('queues.index') }}" class="flex flex-col items-center justify-center rounded-xl border border-[#E2E8F0] bg-white p-6 text-center transition hover:border-[#2563EB] hover:bg-[#F8FAFC]">
                        <svg class="h-8 w-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-[#1E293B]">Kelola Antrian</p>
                    </a>
                    @endcan

                    @can('medicine.view')
                    <a href="{{ route('medicines.index') }}" class="flex flex-col items-center justify-center rounded-xl border border-[#E2E8F0] bg-white p-6 text-center transition hover:border-[#2563EB] hover:bg-[#F8FAFC]">
                        <svg class="h-8 w-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-[#1E293B]">Tambah Obat</p>
                    </a>
                    @endcan

                    @can('report.view')
                    <a href="{{ route('reports.visits') }}" class="flex flex-col items-center justify-center rounded-xl border border-[#E2E8F0] bg-white p-6 text-center transition hover:border-[#2563EB] hover:bg-[#F8FAFC]">
                        <svg class="h-8 w-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-[#1E293B]">Lihat Laporan</p>
                    </a>
                    @endcan
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                    <div class="border-b border-[#E2E8F0] px-6 py-4">
                        <h2 class="text-lg font-semibold text-[#1E293B]">Pasien Terbaru</h2>
                    </div>
                    <div class="p-6">
                        @forelse($recentPatients as $patient)
                            <div class="flex items-center justify-between border-b border-[#E2E8F0] py-3 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#DBEAFE]">
                                        <svg class="h-5 w-5 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-[#0F172A]">{{ $patient->name }}</p>
                                        <p class="text-xs text-[#64748B]">{{ $patient->medical_record_number }} • {{ $patient->coverage_type ?? 'Umum' }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('patients.show', $patient) }}" class="text-sm font-medium text-[#2563EB] hover:text-[#1D4ED8]">Detail</a>
                            </div>
                        @empty
                            <p class="text-center text-sm text-[#64748B]">Belum ada pasien terdaftar</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                    <div class="border-b border-[#E2E8F0] px-6 py-4">
                        <h2 class="text-lg font-semibold text-[#1E293B]">Status Sistem & Integrasi</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex items-center justify-between rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#D1FAE5]">
                                    <svg class="h-4 w-4 text-[#16A34A]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-[#0F172A]">SATUSEHAT</p>
                                    <p class="text-xs text-[#64748B]">Terkoneksi • Terakhir: 3 menit lalu</p>
                                </div>
                            </div>
                            <span class="rounded-lg bg-[#D1FAE5] px-3 py-1 text-xs font-semibold text-[#16A34A]">Online</span>
                        </div>

                        <div class="flex items-center justify-between rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#D1FAE5]">
                                    <svg class="h-4 w-4 text-[#16A34A]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-[#0F172A]">BPJS VClaim</p>
                                    <p class="text-xs text-[#64748B]">Aktif • Mode: Live</p>
                                </div>
                            </div>
                            <span class="rounded-lg bg-[#D1FAE5] px-3 py-1 text-xs font-semibold text-[#16A34A]">Online</span>
                        </div>

                        <div class="flex items-center justify-between rounded-xl border border-[#FEF3C7] bg-[#FFFBEB] p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#FEF3C7]">
                                    <svg class="h-4 w-4 text-[#F59E0B]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-[#0F172A]">Stok Obat Kritis</p>
                                    <p class="text-xs text-[#64748B]">{{ $metrics['medicine_low'] ?? 0 }} item di bawah batas minimum</p>
                                </div>
                            </div>
                            <span class="rounded-lg bg-[#FEF3C7] px-3 py-1 text-xs font-semibold text-[#F59E0B]">Lihat</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    @if (!$isDoctor)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Tren Kunjungan 7 Hari
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            @php
                $labels = [];
                $data = [];
                foreach($dailyTrend ?? [] as $day) {
                    $labels[] = \Carbon\Carbon::parse($day['date'])->format('d M');
                    $data[] = $day['total'];
                }
            @endphp
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Kunjungan',
                        data: {!! json_encode($data) !!},
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#2563EB',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 12 },
                            borderColor: '#E2E8F0',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F1F5F9' },
                            ticks: { font: { size: 11 }, color: '#64748B' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#64748B' }
                        }
                    }
                }
            });
        }

        // Kunjungan Berdasarkan Pembiayaan
        const coverageCtx = document.getElementById('coverageChart');
        if (coverageCtx) {
            new Chart(coverageCtx, {
                type: 'bar',
                data: {
                    labels: ['BPJS', 'Umum'],
                    datasets: [{
                        label: 'Kunjungan',
                        data: [{{ $metrics['bpjs_count'] ?? 0 }}, {{ $metrics['umum_count'] ?? 0 }}],
                        backgroundColor: ['#2563EB', '#2563EB'],
                        borderRadius: 8,
                        barThickness: 80,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 12 },
                            borderColor: '#E2E8F0',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F1F5F9' },
                            ticks: { font: { size: 11 }, color: '#64748B' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#64748B' }
                        }
                    }
                }
            });
        }
    </script>
    @endif
    @endpush
</x-app-layout>
