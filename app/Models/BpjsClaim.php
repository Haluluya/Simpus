<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpjsClaim extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'visit_id',
        'performed_by',
        'interaction_type',
        'request_method',
        'endpoint',
        'external_reference',
        'status_code',
        'status_message',
        'response_time_ms',
        'headers',
        'raw_request',
        'raw_response',
        'performed_at',
        'signature',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'headers' => 'array',
        'performed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function performedByUser()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
