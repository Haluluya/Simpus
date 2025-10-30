<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] px-6 py-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#1E293B]">Laporan Kunjungan</h1>
                    <p class="mt-1 text-sm text-[#64748B]">Analisis dan laporan kunjungan pasien</p>
                </div>
                <a href="{{ route('reports.visits', array_merge(request()->all(), ['format' => 'pdf'])) }}" class="flex h-10 items-center gap-2 rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm font-semibold text-[#0F172A] shadow-sm transition hover:bg-[#F8FAFC]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Unduh PDF
                </a>
            </div>

            <form method="GET" class="mb-6 rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                <div class="mb-4">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Periode</label>
                    <div class="grid grid-cols-4 gap-4">
                        <select name="month" class="h-10 rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                            <option value="">Bulan</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" @selected(request('month') == $i)>{{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                            @endfor
                        </select>
                        <select name="day" class="h-10 rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                            <option value="">Hari</option>
                            @for($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}" @selected(request('day') == $i)>{{ $i }}</option>
                            @endfor
                        </select>
                        <select name="year" class="h-10 rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                            <option value="">Tahun</option>
                            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" @selected(request('year') == $i)>{{ $i }}</option>
                            @endfor
                        </select>
                        <select name="clinic" class="h-10 rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                            <option value="">Semua Poli</option>
                            @foreach($clinics ?? [] as $clinic)
                                <option value="{{ $clinic->id }}" @selected(request('clinic') == $clinic->id)>{{ $clinic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-[#64748B]">Pembiayaan</label>
                        <select name="coverage_type" class="h-10 w-full rounded-xl border border-[#CBD5E1] bg-white px-4 text-sm text-[#0F172A] transition focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#DBEAFE]">
                            <option value="">Semua</option>
                            <option value="bpjs" @selected(request('coverage_type') === 'bpjs')>BPJS</option>
                            <option value="umum" @selected(request('coverage_type') === 'umum')>Umum</option>
                        </select>
                    </div>
                    <div class="col-span-2 flex items-end gap-3">
                        <button type="submit" class="flex h-10 items-center gap-2 rounded-xl bg-[#2563EB] px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1D4ED8]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Terapkan Filter
                        </button>
                        <a href="{{ route('reports.visits') }}" class="flex h-10 items-center gap-2 rounded-xl border border-[#CBD5E1] bg-white px-6 text-sm font-semibold text-[#0F172A] shadow-sm transition hover:bg-[#F8FAFC]">Reset</a>
                    </div>
                </div>
            </form>

            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#64748B]">Total Kunjungan</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ number_format($totalVisits ?? 0) }}</p>
                            <p class="mt-1 text-xs text-[#16A34A]">+12% dari periode lalu</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DBEAFE]">
                            <svg class="h-6 w-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#64748B]">Kunjungan BPJS</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ number_format($bpjsVisits ?? 0) }}</p>
                            <p class="mt-1 text-xs text-[#64748B]">63% dari total</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DBEAFE]">
                            <svg class="h-6 w-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#64748B]">Kunjungan Umum</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ number_format($umumVisits ?? 0) }}</p>
                            <p class="mt-1 text-xs text-[#DC2626]">-8% dari total</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DBEAFE]">
                            <svg class="h-6 w-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#64748B]">Rata-rata Harian</p>
                            <p class="mt-2 text-3xl font-bold text-[#0F172A]">{{ number_format($avgDaily ?? 0) }}</p>
                            <p class="mt-1 text-xs text-[#64748B]">kunjungan/hari</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#DBEAFE]">
                            <svg class="h-6 w-6 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 rounded-2xl border border-[#E2E8F0] bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-[#1E293B]">Tren Kunjungan Harian</h2>
                <div class="h-80"><canvas id="visitChart"></canvas></div>
            </div>

            <div class="rounded-2xl border border-[#E2E8F0] bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] px-6 py-4">
                    <h2 class="text-lg font-semibold text-[#1E293B]">Detail Kunjungan per Poli</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Poli</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Total Kunjungan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">BPJS</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">Umum</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#64748B]">% BPJS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0] bg-white">
                            @forelse($visitDetails ?? [] as $detail)
                                <tr class="transition hover:bg-[#F8FAFC]">
                                    <td class="px-6 py-4 text-sm text-[#0F172A]">{{ $detail['date'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-[#0F172A]">{{ $detail['clinic'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-[#0F172A]">{{ number_format($detail['total'] ?? 0) }}</td>
                                    <td class="px-6 py-4 text-sm text-[#2563EB]">{{ number_format($detail['bpjs'] ?? 0) }}</td>
                                    <td class="px-6 py-4 text-sm text-[#64748B]">{{ number_format($detail['umum'] ?? 0) }}</td>
                                    <td class="px-6 py-4 text-sm text-[#64748B]">{{ number_format($detail['bpjs_percentage'] ?? 0, 1) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-[#64748B]">Tidak ada data untuk periode yang dipilih</p>
                                    </td>
                                </tr>
                            @endforelse
                            @if(isset($visitDetails) && count($visitDetails) > 0)
                                <tr class="bg-[#F8FAFC] font-semibold">
                                    <td class="px-6 py-4 text-sm text-[#0F172A]" colspan="2">Total</td>
                                    <td class="px-6 py-4 text-sm text-[#0F172A]">{{ number_format($totalVisits ?? 0) }}</td>
                                    <td class="px-6 py-4 text-sm text-[#2563EB]">{{ number_format($bpjsVisits ?? 0) }}</td>
                                    <td class="px-6 py-4 text-sm text-[#64748B]">{{ number_format($umumVisits ?? 0) }}</td>
                                    <td class="px-6 py-4 text-sm text-[#64748B]">{{ $totalVisits > 0 ? number_format(($bpjsVisits / $totalVisits) * 100, 1) : 0 }}%</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('visitChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels ?? ['22 Des', '23 Des', '24 Des', '25 Des', '26 Des', '27 Des', '28 Des']) !!},
                    datasets: [{
                        label: 'Kunjungan',
                        data: {!! json_encode($chartData ?? [45, 52, 48, 65, 59, 80, 95]) !!},
                        backgroundColor: '#2563EB',
                        borderRadius: 8,
                        barThickness: 40,
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
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            borderColor: '#E2E8F0',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F1F5F9', drawBorder: false },
                            ticks: { font: { size: 12 }, color: '#64748B' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 12 }, color: '#64748B' }
                        }
                    }
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
