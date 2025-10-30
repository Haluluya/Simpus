<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VisitsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @param Collection<int, \App\Models\Visit> $visits
     */
    public function __construct(private readonly Collection $visits)
    {
    }

    public function collection(): Collection
    {
        return $this->visits->values();
    }

    public function headings(): array
    {
        return [
            'No',
            'Visit Date',
            'Patient Name',
            'Medical Record Number',
            'NIK',
            'Coverage',
            'Clinic',
            'Provider',
            'Status',
        ];
    }

    /**
     * @param \App\Models\Visit $visit
     */
    public function map($visit): array
    {
        static $row = 0;
        $row++;

        return [
            $row,
            $visit->visit_datetime ? Carbon::parse($visit->visit_datetime)->format('Y-m-d H:i') : '',
            $visit->patient?->name,
            $visit->patient?->medical_record_number,
            $visit->patient?->nik,
            $visit->coverage_type,
            $visit->clinic_name,
            $visit->provider?->name,
            $visit->status,
        ];
    }
}
