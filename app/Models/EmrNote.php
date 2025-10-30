<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmrNote extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'visit_id',
        'author_id',
        'subjective',
        'objective',
        'assessment',
        'plan',
        'icd10_code',
        'icd10_description',
        'notes',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'meta' => 'array',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
