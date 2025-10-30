<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReferralStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('referral.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['PENDING', 'SENT', 'COMPLETED', 'CANCELLED'])],
            'responded_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
