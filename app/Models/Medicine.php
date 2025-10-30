<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'satuan',
        'stok',
        'stok_minimal',
        'keterangan',
    ];

    protected $casts = [
        'stok' => 'integer',
        'stok_minimal' => 'integer',
    ];

    public function scopePerluRestok($query)
    {
        return $query->whereColumn('stok', '<=', 'stok_minimal');
    }
}
