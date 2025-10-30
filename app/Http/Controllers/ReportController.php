<?php

namespace App\Http\Controllers;

use App\Exports\VisitsExport;
use App\Models\Visit;
use App\Support\Audit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function visitsMonthly(Request $request)
    {
        $startDate = filled($request->input('start_date'))
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $endDate = filled($request->input('end_date'))
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth()->endOfDay();
        $coverage = $request->query('coverage_type');
        $clinic = $request->query('clinic_name');
        $format = strtolower((string) $request->query('format', ''));

        $visitsQuery = Visit::query()
            ->with(['patient:id,name,medical_record_number,nik', 'provider:id,name'])
            ->whereBetween('visit_datetime', [$startDate, $endDate])
            ->when($coverage, fn ($query) => $query->where('coverage_type', $coverage))
            ->when($clinic, fn ($query) => $query->where('clinic_name', 'like', "%{$clinic}%"))
            ->orderBy('visit_datetime');

        $visits = $visitsQuery->get();

        $summary = [
            'total_visits' => $visits->count(),
            'bpjs' => $visits->where('coverage_type', 'BPJS')->count(),
            'umum' => $visits->where('coverage_type', 'UMUM')->count(),
        ];

        $daily = $visits->groupBy(fn ($visit) => $visit->visit_datetime?->format('Y-m-d') ?? 'N/A')
            ->map->count();

        $filters = [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'coverage_type' => $coverage,
            'clinic_name' => $clinic,
        ];

        if ($format === 'pdf') {
            $this->ensureExportPermission($request);

            $pdf = Pdf::setOptions(['isRemoteEnabled' => true, 'dpi' => 96])
                ->loadView('reports.visits_pdf', [
                    'visits' => $visits,
                    'summary' => $summary,
                    'filters' => $filters,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ]);

            $fileName = Str::slug('visit-report-'.$filters['start_date'].'-'.$filters['end_date']).'.pdf';

            $this->logReportExport('pdf', $filters, $visits->count());

            return $pdf->download($fileName);
        }

        if ($format === 'xlsx') {
            $this->ensureExportPermission($request);

            $fileName = Str::slug('visit-report-'.$filters['start_date'].'-'.$filters['end_date']).'.xlsx';

            $this->logReportExport('xlsx', $filters, $visits->count());

            return Excel::download(new VisitsExport($visits), $fileName);
        }

        // Prepare data for view
        $clinics = Visit::query()
            ->select('clinic_name')
            ->distinct()
            ->whereNotNull('clinic_name')
            ->orderBy('clinic_name')
            ->pluck('clinic_name')
            ->map(fn($name) => (object)['id' => $name, 'name' => $name]);

        $totalVisits = $summary['total_visits'];
        $bpjsVisits = $summary['bpjs'];
        $umumVisits = $summary['umum'];
        $avgDaily = $totalVisits > 0 ? round($totalVisits / max(1, $startDate->diffInDays($endDate) + 1), 1) : 0;

        // Prepare chart data
        $chartLabels = [];
        $chartData = [];
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->addDay());
        foreach ($period as $date) {
            $dateKey = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d M');
            $chartData[] = $daily[$dateKey] ?? 0;
        }

        // Prepare detail table data
        $visitDetails = $visits->groupBy(fn($v) => ($v->visit_datetime?->format('Y-m-d') ?? 'N/A') . '|' . ($v->clinic_name ?? 'N/A'))
            ->map(function($group, $key) {
                [$date, $clinic] = explode('|', $key);
                $total = $group->count();
                $bpjs = $group->where('coverage_type', 'BPJS')->count();
                $umum = $group->where('coverage_type', 'UMUM')->count();
                return [
                    'date' => Carbon::parse($date)->format('d/m/Y'),
                    'clinic' => $clinic,
                    'total' => $total,
                    'bpjs' => $bpjs,
                    'umum' => $umum,
                    'bpjs_percentage' => $total > 0 ? ($bpjs / $total) * 100 : 0,
                ];
            })->values()->all();

        return view('reports.visits', [
            'visits' => $visits,
            'summary' => $summary,
            'filters' => $filters,
            'dailyCounts' => $daily,
            'clinics' => $clinics,
            'totalVisits' => $totalVisits,
            'bpjsVisits' => $bpjsVisits,
            'umumVisits' => $umumVisits,
            'avgDaily' => $avgDaily,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'visitDetails' => $visitDetails,
        ]);
    }

    private function ensureExportPermission(Request $request): void
    {
        if (! $request->user()?->can('report.export')) {
            abort(403, __('You do not have permission to export reports.'));
        }
    }

    private function logReportExport(string $format, array $filters, int $records): void
    {
        Audit::log(
            'EXPORT', 
            'Report', 
            null, 
            null,
            [
                'format' => $format,
                'filters' => $filters,
                'records' => $records,
            ],
            "Ekspor laporan kunjungan dalam format {$format} dengan {$records} record",
            'success'
        );
    }
}
