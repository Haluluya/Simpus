<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'visit_id',
        'tanggal_antrian',
        'nomor_antrian',
        'department',
        'status',
        'meta',
    ];

    protected $casts = [
        'tanggal_antrian' => 'date',
        'meta' => 'array',
    ];

    public const STATUS_WAITING = 'MENUNGGU';
    public const STATUS_CALLING = 'DIPANGGIL';
    public const STATUS_DONE = 'SELESAI';
    public const STATUS_CANCELLED = 'BATAL';

    public static function statuses(): array
    {
        return [
            self::STATUS_WAITING,
            self::STATUS_CALLING,
            self::STATUS_DONE,
            self::STATUS_CANCELLED,
        ];
    }

    public static function nextNumberForDate($date): string
    {
        $dateString = $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : (string) $date;
        $count = static::whereDate('tanggal_antrian', $dateString)->count() + 1;

        return 'A'.str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
