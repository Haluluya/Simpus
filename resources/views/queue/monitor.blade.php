<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-subtitle">Integrasi Nasional</p>
            <h1 class="page-title">Monitoring Antrian Sinkronisasi</h1>
        </div>
    </x-slot>

    <section class="grid gap-6 lg:grid-cols-3">
        <div class="card">
            <div class="card-body">
                <p class="stat-label">Menunggu Diproses</p>
                <p class="mt-4 text-3xl font-semibold text-slate-900">{{ number_format($summary['PENDING'] ?? 0) }}</p>
                <p class="mt-2 text-xs text-slate-500">Rata-rata percobaan ulang: {{ $pendingAverage }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="stat-label">Berhasil Terkirim</p>
                <p class="mt-4 text-3xl font-semibold text-success">{{ number_format($summary['SENT'] ?? 0) }}</p>
                <p class="mt-2 text-xs text-slate-500">
                    Per target: {{ collect($byTarget)->map(fn ($total, $target) => "{$target}: {$total}")->implode(', ') ?: 'Belum ada' }}
                </p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="stat-label">Gagal / Error</p>
                <p class="mt-4 text-3xl font-semibold text-danger">{{ number_format($errorCount) }}</p>
                <p class="mt-2 text-xs text-slate-500">Segera lakukan penelusuran dan ulangi proses.</p>
            </div>
        </div>
    </section>

    <section class="card mt-6">
        <div class="card-header">
            <h2 class="section-title">Aktivitas Antrian Terbaru</h2>
        </div>
        <div class="card-body">
            @if ($recentJobs->isEmpty())
                <x-empty
                    title="Belum ada aktivitas"
                    message="Integrasi BPJS atau SATUSEHAT akan muncul di sini setelah proses sinkronisasi dijalankan."
                    icon="queue-list" />
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Target</th>
                            <th>Entitas</th>
                            <th>Status</th>
                            <th>Percobaan</th>
                            <th>Pembaruan Terakhir</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                        @foreach ($recentJobs as $job)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $job->id }}</td>
                                <td class="text-sm text-slate-700">{{ $job->target }}</td>
                                <td class="text-sm text-slate-700">
                                    {{ class_basename($job->entity_type) }} #{{ $job->entity_id }}
                                </td>
                                <td>
                                    @php
                                        $statusBadge = [
                                            'PENDING' => 'badge-warning',
                                            'PROCESSING' => 'badge-info',
                                            'SENT' => 'badge-success',
                                            'ERROR' => 'badge-danger',
                                        ][$job->status] ?? 'badge-info';
                                    @endphp
                                    <span class="badge {{ $statusBadge }}">{{ \Illuminate\Support\Str::headline(strtolower($job->status)) }}</span>
                                    @if ($job->last_error && $job->status === 'ERROR')
                                        <p class="mt-1 text-xs text-danger">{{ \Illuminate\Support\Str::limit($job->last_error, 80) }}</p>
                                    @endif
                                </td>
                                <td class="text-sm text-slate-700">{{ $job->attempts }}</td>
                                <td class="text-sm text-slate-700">{{ $job->updated_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
