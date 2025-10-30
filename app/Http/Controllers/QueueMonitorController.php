<?php

namespace App\Http\Controllers;

use App\Models\SyncQueue;
use Illuminate\Support\Facades\DB;

class QueueMonitorController extends Controller
{
    public function index()
    {
        $summary = SyncQueue::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $byTarget = SyncQueue::select('target', DB::raw('count(*) as total'))
            ->groupBy('target')
            ->pluck('total', 'target')
            ->toArray();

        $recentJobs = SyncQueue::with(['entity'])
            ->latest('updated_at')
            ->limit(15)
            ->get();

        $pendingAverage = SyncQueue::where('status', 'PENDING')->avg('attempts');
        $errorCount = $summary['ERROR'] ?? 0;

        return view('queue.monitor', [
            'summary' => $summary,
            'byTarget' => $byTarget,
            'recentJobs' => $recentJobs,
            'pendingAverage' => round($pendingAverage ?? 0, 2),
            'errorCount' => $errorCount,
        ]);
    }
}
