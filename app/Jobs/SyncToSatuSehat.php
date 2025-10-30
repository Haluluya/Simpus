<?php

namespace App\Jobs;

use App\Models\SyncQueue;
use App\Services\Fhir\FhirMapper;
use App\Services\SatuSehat\SatuSehatClient;
use App\Support\Audit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncToSatuSehat implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $syncQueueId)
    {
        $this->onQueue('satusehat');
    }

    public function handle(SatuSehatClient $client, FhirMapper $mapper): void
    {
        $record = SyncQueue::find($this->syncQueueId);

        if (! $record || $record->target !== 'SATUSEHAT') {
            return;
        }

        $record->updateQuietly([
            'status' => 'PROCESSING',
            'attempts' => $record->attempts + 1,
            'locked_at' => now(),
        ]);

        try {
            $entity = $this->resolveEntity($record);

            if (! $entity) {
                throw new \RuntimeException('Entity not found for sync queue ID '.$record->id);
            }

            $payload = $record->payload ?? $mapper->map($entity);
            $resourceType = $payload['resourceType'] ?? $this->resolveResourceType($entity);

            $response = $client->postResource($resourceType, $payload);

            $record->updateQuietly([
                'status' => 'SENT',
                'payload' => $payload,
                'last_synced_at' => now(),
                'locked_at' => null,
                'failed_at' => null,
                'last_error' => null,
                'meta' => array_merge($record->meta ?? [], [
                    'satusehat_response' => $response,
                ]),
            ]);

            Audit::log('satusehat_sync_success', SyncQueue::class, $record->id, null, [
                'resource_type' => $resourceType,
                'entity_id' => $record->entity_id,
            ]);
        } catch (\Throwable $exception) {
            $record->updateQuietly([
                'status' => 'ERROR',
                'last_error' => $exception->getMessage(),
                'locked_at' => null,
                'failed_at' => now(),
            ]);

            Log::error('SyncToSatuSehat failed', [
                'sync_queue_id' => $record->id,
                'error' => $exception->getMessage(),
            ]);

            Audit::log('satusehat_sync_failed', SyncQueue::class, $record->id, null, [
                'entity_id' => $record->entity_id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function resolveEntity(SyncQueue $record): mixed
    {
        if (! class_exists($record->entity_type)) {
            return null;
        }

        return $record->entity_type::find($record->entity_id);
    }

    private function resolveResourceType(mixed $entity): string
    {
        return match (true) {
            $entity instanceof \App\Models\Patient => 'Patient',
            $entity instanceof \App\Models\Visit => 'Encounter',
            default => throw new \InvalidArgumentException('Unable to infer resource type from entity '.get_debug_type($entity)),
        };
    }
}
