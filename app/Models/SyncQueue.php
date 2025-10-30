<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncQueue extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'sync_queue';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'target',
        'status',
        'attempts',
        'correlation_id',
        'payload',
        'last_error',
        'available_at',
        'last_synced_at',
        'locked_at',
        'failed_at',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
        'available_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'locked_at' => 'datetime',
        'failed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function entity()
    {
        return $this->morphTo();
    }
}
