<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'visit_id',
        'user_id_doctor',
        'status',
        'catatan',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'user_id_doctor');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
