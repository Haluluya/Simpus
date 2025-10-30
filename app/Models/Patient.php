<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'medical_record_number',
        'nik',
        'bpjs_card_no',
        'name',
        'date_of_birth',
        'gender',
        'blood_type',
        'phone',
        'email',
        'address',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'occupation',
        'allergies',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'meta',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'meta' => 'array',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function latestVisit()
    {
        return $this->hasOne(Visit::class)->latestOfMany();
    }

    public function emrNotes()
    {
        return $this->hasManyThrough(EmrNote::class, Visit::class);
    }

    public function labOrders()
    {
        return $this->hasManyThrough(LabOrder::class, Visit::class);
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
