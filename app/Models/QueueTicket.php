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
        'doctor',
        'payment_method',
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

    public static function nextNumberForDate($date, ?string $department = null): string
    {
        $dateString = $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : (string) $date;
        $department = static::normalizeDepartment($department);

        $builder = static::query()
            ->whereDate('tanggal_antrian', $dateString)
            ->when($department, function ($query) use ($department) {
                $query->where('department', $department);
            }, function ($query) {
                $query->where(function ($inner) {
                    $inner->whereNull('department')
                        ->orWhere('department', '');
                });
            });

        $length = (int) config('queue_ticket.number_length', 3);

        // Use database locking to prevent race condition
        $lastTicket = (clone $builder)
            ->latest('id')
            ->lockForUpdate()
            ->first();

        if ($lastTicket && preg_match('/^([A-Z]+)(\d+)$/i', $lastTicket->nomor_antrian, $matches)) {
            $prefix = strtoupper($matches[1]);
            $digits = strlen($matches[2]);
            $nextNumber = (int) $matches[2] + 1;

            return $prefix . str_pad((string) $nextNumber, max($digits, $length), '0', STR_PAD_LEFT);
        }

        $prefix = static::prefixForDepartment($department);

        // Count with lock to prevent race condition
        $count = (clone $builder)->lockForUpdate()->count() + 1;

        return $prefix . str_pad((string) $count, $length, '0', STR_PAD_LEFT);
    }

    protected static function normalizeDepartment(?string $department): ?string
    {
        if ($department === null) {
            return null;
        }

        $trimmed = trim($department);

        return $trimmed !== '' ? $trimmed : null;
    }

    protected static function prefixForDepartment(?string $department): string
    {
        $prefixes = config('queue_ticket.prefixes', []);

        if ($department && isset($prefixes[$department])) {
            return strtoupper($prefixes[$department]);
        }

        if ($department) {
            $generated = static::generatePrefixFromDepartment($department);

            if ($generated !== '') {
                return $generated;
            }
        }

        return strtoupper(config('queue_ticket.default_prefix', 'A'));
    }

    protected static function generatePrefixFromDepartment(string $department): string
    {
        $cleaned = preg_replace('/[^A-Za-z0-9\s]/', ' ', $department);
        $parts = array_filter(explode(' ', $cleaned));
        $initials = array_map(static function ($part) {
            return strtoupper(substr($part, 0, 1));
        }, $parts);

        $prefix = implode('', $initials);

        if ($prefix === '') {
            return '';
        }

        return substr($prefix, 0, 3);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Log when nomor_antrian is being updated
        static::updating(function ($queueTicket) {
            if ($queueTicket->isDirty('nomor_antrian')) {
                \Log::info('QueueTicket nomor_antrian updated', [
                    'queue_ticket_id' => $queueTicket->id,
                    'old_nomor_antrian' => $queueTicket->getOriginal('nomor_antrian'),
                    'new_nomor_antrian' => $queueTicket->nomor_antrian,
                    'tanggal_antrian' => $queueTicket->tanggal_antrian,
                    'patient_id' => $queueTicket->patient_id,
                    'updated_at' => now(),
                    'updated_by' => auth()->id() ?? 'system',
                    'trace' => collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10))
                        ->pluck('file', 'function')
                        ->take(5)
                        ->toArray()
                ]);
            }
        });
        
        // Log when queue ticket is created
        static::creating(function ($queueTicket) {
            \Log::info('QueueTicket creating', [
                'patient_id' => $queueTicket->patient_id,
                'nomor_antrian' => $queueTicket->nomor_antrian,
                'tanggal_antrian' => $queueTicket->tanggal_antrian,
                'department' => $queueTicket->department,
                'creating_at' => now()
            ]);
        });
        
        // Log when queue ticket is updated
        static::updating(function ($queueTicket) {
            if ($queueTicket->isDirty()) {
                $dirtyFields = $queueTicket->getDirty();
                \Log::info('QueueTicket updated', [
                    'queue_ticket_id' => $queueTicket->id,
                    'changed_fields' => $dirtyFields,
                    'patient_id' => $queueTicket->patient_id,
                    'tanggal_antrian' => $queueTicket->tanggal_antrian
                ]);
                
                if (array_key_exists('nomor_antrian', $dirtyFields)) {
                    \Log::info('QueueTicket nomor_antrian specifically changed', [
                        'queue_ticket_id' => $queueTicket->id,
                        'old_value' => $queueTicket->getOriginal('nomor_antrian'),
                        'new_value' => $dirtyFields['nomor_antrian']
                    ]);
                }
            }
        });
    }
}
