<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Redirect Petugas Lab langsung ke Lab Work Queue
        if ($user && method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('lab.view') && !$user->hasPermissionTo('visit.view')) {
            return redirect()->route('lab.index');
        }
        
        // Redirect Petugas Apotek langsung ke Antrean Resep (cek permission dengan try-catch)
        try {
            if ($user && method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('pharmacy.view') && !$user->hasPermissionTo('visit.view')) {
                return redirect()->route('pharmacy.index');
            }
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            // Permission belum ada, skip redirect
        }
        
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $rangeStart = Carbon::now()->subDays(29)->startOfDay();
        $rangeEnd = Carbon::now()->endOfDay();

        // Cache metrics untuk 5 menit untuk mengurangi query berulang
        $cacheKey = 'dashboard_metrics_' . $today->format('Y-m-d');
        
        $metrics = Cache::remember($cacheKey, 300, function () use ($today, $startOfMonth, $rangeEnd) {
            $visitsToday = Schema::hasTable('visits')
                ? Visit::whereDate('visit_datetime', $today)->count()
                : 0;

            $visitsThisMonth = Schema::hasTable('visits')
                ? Visit::whereBetween('visit_datetime', [$startOfMonth, $rangeEnd])->count()
                : 0;

            $coverageBreakdown = Schema::hasTable('visits')
                ? Visit::selectRaw('coverage_type, COUNT(*) as total')
                    ->whereBetween('visit_datetime', [$startOfMonth, $rangeEnd])
                    ->groupBy('coverage_type')
                    ->pluck('total', 'coverage_type')
                    ->toArray()
                : [];

            $totalPatients = Schema::hasTable('patients') ? Patient::count() : 0;

            return [
                'visits_today' => $visitsToday,
                'visits_this_month' => $visitsThisMonth,
                'bpjs_count' => $coverageBreakdown['BPJS'] ?? 0,
                'umum_count' => $coverageBreakdown['UMUM'] ?? 0,
                'total_patients' => $totalPatients,
            ];
        });

        // Data yang selalu realtime (tidak di-cache)
        $queueWaiting = Schema::hasTable('queue_tickets')
            ? QueueTicket::whereDate('tanggal_antrian', $today)
                ->where('status', QueueTicket::STATUS_WAITING)
                ->count()
            : 0;

        $medicineLow = Schema::hasTable('medicines')
            ? Medicine::perluRestok()->count()
            : 0;

        // Daily trend - cache 10 menit
        $dailyTrend = Cache::remember('dashboard_daily_trend_' . $today->format('Y-m-d'), 600, function () use ($rangeStart, $rangeEnd) {
            return Schema::hasTable('visits')
                ? Visit::selectRaw('DATE(visit_datetime) as date, COUNT(*) as total')
                    ->whereBetween('visit_datetime', [$rangeStart, $rangeEnd])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function ($row) {
                        return [
                            'date' => $row->date,
                            'total' => (int) $row->total,
                        ];
                    })
                : collect();
        });

        $recentPatients = Schema::hasTable('patients')
            ? Patient::latest()->limit(5)->select('id', 'name', 'medical_record_number', 'created_at')->get()
            : collect();

        // Mode khusus Dokter: tampilkan antrean poli dokter sebagai beranda
        $user = $request->user();
        $isDoctor = method_exists($user, 'hasRole') ? $user->hasRole('doctor') : false;
        $doctorQueues = collect();

        if ($isDoctor && Schema::hasTable('queue_tickets')) {
            $department = (string) ($user->department ?? '');
            
            // Dokter hanya melihat antrean sesuai poli/department mereka
            // Jika department kosong (misal: Dokter Umum tanpa spesialisasi), lihat semua antrean
            $doctorQueues = QueueTicket::query()
                ->with([
                    'patient:id,name,medical_record_number',
                    'visit:id,patient_id,clinic_name,visit_number',
                ])
                ->whereDate('tanggal_antrian', $today)
                ->where('status', QueueTicket::STATUS_WAITING)
                ->when($department !== '', function ($query) use ($department) {
                    // Filter berdasarkan department dokter
                    // Hanya tampilkan antrean yang belum punya visit ATAU visit dengan clinic_name sesuai department
                    $query->where(function ($q) use ($department) {
                        $q->where('department', $department)
                          ->orWhere('department', null)
                          ->orWhere('department', '');
                    });
                })
                ->orderBy('nomor_antrian')
                ->get();
        }

        return view('dashboard', [
            'metrics' => array_merge($metrics, [
                'queue_waiting' => $queueWaiting,
                'medicine_low' => $medicineLow,
            ]),
            'dailyTrend' => $dailyTrend,
            'recentPatients' => $recentPatients,
            'isDoctor' => $isDoctor,
            'doctorQueues' => $doctorQueues,
        ]);
    }
}
