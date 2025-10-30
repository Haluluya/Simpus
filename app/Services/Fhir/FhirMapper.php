<?php

namespace App\Services\Fhir;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Support\Arr;

class FhirMapper
{
    private string $organizationId;
    private ?string $facilityId;

    public function __construct()
    {
        $this->organizationId = (string) config('satusehat.organization_id', 'organization-id');
        $this->facilityId = config('satusehat.facility_id');
    }

    public function map(object $entity): array
    {
        if ($entity instanceof Patient) {
            return $this->patient($entity);
        }

        if ($entity instanceof Visit) {
            return $this->encounter($entity);
        }

        throw new \InvalidArgumentException('Unsupported entity type for FHIR mapping: '.get_debug_type($entity));
    }

    public function patient(Patient $patient): array
    {
        return [
            'resourceType' => 'Patient',
            'identifier' => [
                [
                    'system' => 'https://fhir.kemkes.go.id/id/nik',
                    'value' => $patient->nik,
                ],
                [
                    'system' => 'https://simpus.local/id/medical-record-number',
                    'value' => $patient->medical_record_number,
                ],
            ],
            'name' => [
                [
                    'use' => 'official',
                    'text' => $patient->name,
                ],
            ],
            'telecom' => Arr::whereNotNull([
                $patient->phone ? ['system' => 'phone', 'value' => $patient->phone, 'use' => 'mobile'] : null,
                $patient->email ? ['system' => 'email', 'value' => $patient->email] : null,
            ]),
            'gender' => $patient->gender === 'male' ? 'male' : 'female',
            'birthDate' => $patient->date_of_birth?->format('Y-m-d'),
            'address' => [
                [
                    'use' => 'home',
                    'line' => array_filter([$patient->address]),
                    'city' => $patient->city,
                    'district' => $patient->district,
                    'state' => $patient->province,
                    'postalCode' => $patient->postal_code,
                    'country' => 'ID',
                ],
            ],
            'extension' => [
                [
                    'url' => 'https://fhir.kemkes.go.id/StructureDefinition/patient-occupation',
                    'valueCodeableConcept' => [
                        'text' => $patient->occupation ?: 'Tidak diketahui',
                    ],
                ],
            ],
            'meta' => [
                'tag' => [
                    ['system' => 'https://fhir.kemkes.go.id/id/organization', 'code' => $this->organizationId],
                ],
            ],
        ];
    }

    public function encounter(Visit $visit): array
    {
        return [
            'resourceType' => 'Encounter',
            'status' => $this->mapEncounterStatus($visit->status),
            'class' => [
                'system' => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                'code' => 'AMB',
                'display' => 'ambulatory',
            ],
            'subject' => [
                'reference' => 'Patient/'.$visit->patient->nik,
            ],
            'period' => [
                'start' => $visit->visit_datetime?->toAtomString(),
            ],
            'serviceProvider' => [
                'reference' => 'Organization/'.$this->organizationId,
            ],
            'participant' => array_filter([
                $visit->provider_id ? [
                    'individual' => [
                        'reference' => 'Practitioner/'.$visit->provider_id,
                        'display' => $visit->provider?->name,
                    ],
                ] : null,
            ]),
            'location' => $this->facilityId ? [[
                'location' => [
                    'reference' => 'Location/'.$this->facilityId,
                ],
            ]] : [],
            'identifier' => [
                [
                    'system' => 'https://simpus.local/id/visit-number',
                    'value' => $visit->visit_number,
                ],
            ],
        ];
    }

    private function mapEncounterStatus(string $status): string
    {
        return match ($status) {
            'SCHEDULED' => 'planned',
            'ONGOING' => 'in-progress',
            'COMPLETED' => 'finished',
            'CANCELLED' => 'cancelled',
            default => 'unknown',
        };
    }
}
