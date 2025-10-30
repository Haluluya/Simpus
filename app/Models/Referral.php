<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'visit_id',
        'created_by',
        'referral_number',
        'referred_to',
        'referred_department',
        'contact_person',
        'contact_phone',
        'status',
        'scheduled_at',
        'sent_at',
        'responded_at',
        'reason',
        'notes',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
        'meta' => 'array',
    ];

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_SENT = 'SENT';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_SENT,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
