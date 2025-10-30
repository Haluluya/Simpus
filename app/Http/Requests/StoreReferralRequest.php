<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReferralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('referral.create') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'visit_id' => ['nullable', 'exists:visits,id'],
            'referred_to' => ['required', 'string', 'max:255'],
            'referred_department' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'scheduled_at' => ['nullable', 'date'],
            'reason' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['PENDING', 'SENT', 'COMPLETED', 'CANCELLED'])],
        ];
    }
}
