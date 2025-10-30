<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterMedicine extends Model
{
    protected $fillable = [
        'nama_obat',
        'satuan',
        'stok',
    ];

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
