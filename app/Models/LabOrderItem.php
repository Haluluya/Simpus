<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabOrderItem extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'lab_order_id',
        'test_name',
        'loinc_code',
        'specimen_type',
        'result',
        'unit',
        'reference_range',
        'abnormal_flag',
        'result_status',
        'observed_at',
        'resulted_at',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'observed_at' => 'datetime',
        'resulted_at' => 'datetime',
        'meta' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(LabOrder::class, 'lab_order_id');
    }

    public function syncQueue()
    {
        return $this->morphMany(SyncQueue::class, 'entity');
    }
}
