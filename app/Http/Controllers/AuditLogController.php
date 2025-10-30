<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'action',
            'entity_type',
            'user_id',
            'date_from',
            'date_to',
        ]);

        $perPage = (int) $request->query('per_page', 25);
        $perPage = min(100, max(10, $perPage));

        $query = AuditLog::query()->with(['user:id,name', 'user.roles:id,name']);

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (! empty($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $dateFrom = $request->date('date_from');
        if ($dateFrom instanceof Carbon) {
            $query->where('performed_at', '>=', $dateFrom->copy()->startOfDay());
        }

        $dateTo = $request->date('date_to');
        if ($dateTo instanceof Carbon) {
            $query->where('performed_at', '<=', $dateTo->copy()->endOfDay());
        }

        $logs = $query
            ->orderByDesc('performed_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->limit(150)
            ->pluck('action');

        $entityTypes = AuditLog::query()
            ->select('entity_type')
            ->distinct()
            ->orderBy('entity_type')
            ->limit(150)
            ->pluck('entity_type');

        $users = User::query()
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('audit.activity', [
            'logs' => $logs,
            'filters' => $filters,
            'actions' => $actions,
            'entityTypes' => $entityTypes,
            'users' => $users,
            'perPage' => $perPage,
        ]);
    }

    public function show(AuditLog $log)
    {
        $log->load('user:id,name,email');
        
        return response()->json([
            'id' => $log->id,
            'action' => $log->action,
            'entity_type' => $log->entity_type,
            'entity_id' => $log->entity_id,
            'description' => $log->description,
            'user' => [
                'name' => $log->user->name ?? 'System',
                'email' => $log->user->email ?? '-',
            ],
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'status' => $log->status,
            'error_message' => $log->error_message,
            'old_values' => $log->old_values,
            'new_values' => $log->new_values,
            'performed_at' => $log->performed_at->format('d/m/Y H:i:s'),
            'created_at' => $log->created_at->format('d/m/Y H:i:s'),
        ]);
    }
}
