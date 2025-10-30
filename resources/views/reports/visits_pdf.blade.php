<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 12px; color: #1f2937; line-height: 1.4; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .meta { margin-bottom: 12px; font-size: 11px; color: #475569; }
        .summary { margin-top: 12px; display: flex; gap: 12px; }
        .summary div { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        thead th { background: #0f172a; color: #fff; padding: 6px; text-align: left; font-size: 11px; }
        tbody td { border: 1px solid #cbd5e1; padding: 6px; font-size: 11px; }
        tbody tr:nth-child(odd) { background: #f8fafc; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }} &mdash; {{ __('Visit Report') }}</h1>
    <div class="meta">
        <div>{{ __('Period: :start - :end', ['start' => $startDate->format('d M Y'), 'end' => $endDate->format('d M Y')]) }}</div>
        <div>{{ __('Generated at: :time', ['time' => now()->format('d M Y H:i')]) }}</div>
    </div>

    <div class="summary">
        <div>{{ __('Total Visits') }}: <strong>{{ number_format($summary['total_visits']) }}</strong></div>
        <div>{{ __('BPJS') }}: <strong>{{ number_format($summary['bpjs']) }}</strong></div>
        <div>{{ __('UMUM') }}: <strong>{{ number_format($summary['umum']) }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('No') }}</th>
                <th>{{ __('Visit Date') }}</th>
                <th>{{ __('Patient') }}</th>
                <th>{{ __('MRN') }}</th>
                <th>{{ __('Coverage') }}</th>
                <th>{{ __('Clinic') }}</th>
                <th>{{ __('Provider') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($visits as $index => $visit)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ optional($visit->visit_datetime)->format('d M Y H:i') }}</td>
                    <td>{{ $visit->patient->name ?? '-' }}</td>
                    <td>{{ $visit->patient->medical_record_number ?? '-' }}</td>
                    <td>{{ $visit->coverage_type }}</td>
                    <td>{{ $visit->clinic_name }}</td>
                    <td>{{ $visit->provider->name ?? '-' }}</td>
                    <td>{{ $visit->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">{{ __('No data available for this period.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
