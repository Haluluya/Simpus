<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Jobs\SyncToSatuSehat;
use App\Models\Patient;
use App\Models\SyncQueue;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Support\Audit;

class SatuSehatController extends Controller
{
    public function syncPatient(Patient $patient): JsonResponse
    {
        $record = SyncQueue::create([
            'entity_type' => Patient::class,
            'entity_id' => $patient->id,
            'target' => 'SATUSEHAT',
            'status' => 'PENDING',
            'payload' => null,
            'correlation_id' => (string) Str::uuid(),
            'available_at' => now(),
        ]);

        SyncToSatuSehat::dispatch($record->id);

        Audit::log('satusehat_queue_patient', SyncQueue::class, $record->id, null, [
            'entity' => 'patient',
            'patient_id' => $patient->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Patient queued for SATUSEHAT sync.'),
        ]);
    }

    public function syncEncounter(Visit $visit): JsonResponse
    {
        $record = SyncQueue::create([
            'entity_type' => Visit::class,
            'entity_id' => $visit->id,
            'target' => 'SATUSEHAT',
            'status' => 'PENDING',
            'payload' => null,
            'correlation_id' => (string) Str::uuid(),
            'available_at' => now(),
        ]);

        SyncToSatuSehat::dispatch($record->id);

        Audit::log('satusehat_queue_encounter', SyncQueue::class, $record->id, null, [
            'entity' => 'visit',
            'visit_id' => $visit->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Encounter queued for SATUSEHAT sync.'),
        ]);
    }
}
