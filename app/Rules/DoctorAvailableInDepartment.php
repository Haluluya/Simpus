<?php

namespace App\Rules;

use App\Models\Doctor;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DoctorAvailableInDepartment implements ValidationRule
{
    public function __construct(protected ?string $department)
    {
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value || ! $this->department) {
            return;
        }

        // First try database (preferred)
        if ($this->validateFromDatabase($value)) {
            return;
        }

        // Fallback to config for backward compatibility
        if ($this->validateFromConfig($value)) {
            return;
        }

        $fail('Dokter yang dipilih tidak tersedia di poli ini.');
    }

    /**
     * Validate doctor from database.
     */
    protected function validateFromDatabase(string $doctorName): bool
    {
        $availableDoctors = Doctor::getByDepartment($this->department);

        return in_array($doctorName, $availableDoctors, true);
    }

    /**
     * Validate doctor from config (fallback).
     */
    protected function validateFromConfig(string $doctorName): bool
    {
        $doctorsByDepartment = config('doctors.by_department', []);

        if (! isset($doctorsByDepartment[$this->department])) {
            return false;
        }

        return in_array($doctorName, $doctorsByDepartment[$this->department], true);
    }
}
