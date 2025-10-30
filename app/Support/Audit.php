<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Audit
{
    public static function log(
        string $action, 
        string $entityType, 
        ?int $entityId = null, 
        ?array $changes = null, 
        ?array $meta = null,
        ?string $description = null,
        string $status = 'success',
        ?string $errorMessage = null
    ): void
    {
        try {
            $request = request();

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'changes' => empty($changes) ? null : $changes,
                'meta' => empty($meta) ? null : $meta,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'performed_at' => now(),
                'status' => $status,
                'error_message' => $errorMessage,
                'description' => $description,
                'old_values' => $changes['old'] ?? null,
                'new_values' => $changes['new'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Failed to write audit log', [
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
