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
}
