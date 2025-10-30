<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabOrderResult extends Model
{
    protected $fillable = [
        'lab_order_id',
        'nama_tes',
        'hasil',
        'nilai_rujukan',
        'catatan',
        'petugas_lab_id',
    ];

    public function labOrder()
    {
        return $this->belongsTo(LabOrder::class);
    }
    
    public function petugasLab()
    {
        return $this->belongsTo(User::class, 'petugas_lab_id');
    }
}
