<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'visit_id',
        'ordered_by',
        'verified_by',
        'order_number',
        'status',
        'priority',
        'requested_at',
        'processed_at',
        'completed_at',
        'clinical_notes',
        'bpjs_order_reference',
        'fhir_service_request_id',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function orderedByUser()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function items()
    {
        return $this->hasMany(LabOrderItem::class);
    }

    public function syncQueue()
    {
        return $this->morphMany(SyncQueue::class, 'entity');
    }

    public function results()
    {
        return $this->hasMany(LabOrderResult::class);
    }
}
