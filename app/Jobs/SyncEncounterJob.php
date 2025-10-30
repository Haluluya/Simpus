<?php

namespace App\Jobs;

use App\Models\Visit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncEncounterJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $visitId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $visit = Visit::with(['patient', 'provider'])->find($this->visitId);

        if (! $visit) {
            Log::warning('SyncEncounterJob: Visit not found', ['visit_id' => $this->visitId]);

            return;
        }

        $payload = [
            'resourceType' => 'Encounter',
            'status' => 'finished',
            'class' => [
                'system' => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                'code' => 'AMB',
                'display' => 'ambulatory',
            ],
            'subject' => [
                'reference' => 'Patient/'.$visit->patient_id,
                'display' => $visit->patient?->name,
            ],
            'participant' => [
                [
                    'type' => [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType',
                                    'code' => 'ATND',
                                    'display' => 'attender',
                                ],
                            ],
                        ],
                    ],
                    'individual' => [
                        'reference' => 'Practitioner/'.$visit->provider_id,
                        'display' => $visit->provider?->name,
                    ],
                ],
            ],
            'period' => [
                'start' => $visit->visit_datetime?->toISOString(),
                'end' => now()->toISOString(),
            ],
            'location' => [
                [
                    'location' => [
                        'reference' => 'Location/clinic',
                        'display' => $visit->clinic_name,
                    ],
                ],
            ],
        ];

        try {
            $response = Http::timeout(10)
                ->post(url('/api/mock/satusehat/Encounter'), $payload)
                ->json();

            Log::info('SyncEncounterJob success', [
                'visit_id' => $this->visitId,
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('SyncEncounterJob failed', [
                'visit_id' => $this->visitId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
