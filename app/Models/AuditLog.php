<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'changes',
        'meta',
        'ip_address',
        'user_agent',
        'performed_at',
        'status',
        'error_message',
        'old_values',
        'new_values',
        'description',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'changes' => 'array',
        'meta' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'performed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
