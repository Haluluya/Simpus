<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Referral;

class Visit extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'provider_id',
        'visit_number',
        'visit_datetime',
        'clinic_name',
        'coverage_type',
        'sep_no',
        'bpjs_reference_no',
        'queue_number',
        'status',
        'chief_complaint',
        'triage_notes',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'visit_datetime' => 'datetime',
        'meta' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function emrNotes()
    {
        return $this->hasMany(EmrNote::class);
    }

    public function latestEmrNote()
    {
        return $this->hasOne(EmrNote::class)->latestOfMany();
    }

    public function labOrders()
    {
        return $this->hasMany(LabOrder::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function bpjsClaims()
    {
        return $this->hasMany(BpjsClaim::class);
    }

    public function syncQueues()
    {
        return $this->morphMany(SyncQueue::class, 'entity');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function queueTicket()
    {
        return $this->hasOne(QueueTicket::class, 'visit_id');
    }

    /**
     * Ensure queue number consistency with QueueTicket
     */
    public function ensureQueueNumberConsistency()
    {
        $queueTicket = $this->queueTicket;
        
        if ($queueTicket && $this->queue_number !== $queueTicket->nomor_antrian) {
            $originalQueueNumber = $this->queue_number;
            $correctQueueNumber = $queueTicket->nomor_antrian;
            
            \Log::info('Ensuring queue number consistency', [
                'visit_id' => $this->id,
                'original_queue_number' => $originalQueueNumber,
                'correct_queue_number' => $correctQueueNumber,
                'queue_ticket_id' => $queueTicket->id
            ]);
            
            $this->queue_number = $correctQueueNumber;
            $this->save();
            
            return true;
        }
        
        return false;
    }

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Log when queue_number is being updated
        static::updating(function ($visit) {
            if ($visit->isDirty('queue_number')) {
                \Log::info('Visit queue_number updated', [
                    'visit_id' => $visit->id,
                    'old_queue_number' => $visit->getOriginal('queue_number'),
                    'new_queue_number' => $visit->queue_number,
                    'updated_at' => now(),
                    'updated_by' => auth()->id() ?? 'system',
                    'trace' => collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10))
                        ->pluck('file', 'function')
                        ->take(5)
                        ->toArray()
                ]);
            }
        });
        
        // Log when visit is created
        static::creating(function ($visit) {
            \Log::info('Visit creating', [
                'patient_id' => $visit->patient_id,
                'queue_number' => $visit->queue_number,
                'creating_at' => now()
            ]);
        });
        
        // Log when visit is updated
        static::updating(function ($visit) {
            if ($visit->isDirty()) {
                $dirtyFields = $visit->getDirty();
                if (array_key_exists('queue_number', $dirtyFields)) {
                    \Log::info('Visit queue_number specifically changed', [
                        'visit_id' => $visit->id,
                        'old_value' => $visit->getOriginal('queue_number'),
                        'new_value' => $dirtyFields['queue_number'],
                        'all_changes' => $dirtyFields
                    ]);
                }
            }
        });
    }
}
