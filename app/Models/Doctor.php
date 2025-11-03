<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'license_number',
        'specialization',
        'department',
        'phone',
        'email',
        'is_active',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'meta' => 'array',
        ];
    }

    /**
     * Get all active doctors grouped by department.
     * Cached for 1 hour to reduce database queries.
     * Returns empty array if table doesn't exist yet (migration not run).
     */
    public static function getByDepartment(?string $department = null): array
    {
        // Safety check: Return empty array if table doesn't exist yet
        try {
            $cacheKey = $department ? "doctors.department.{$department}" : 'doctors.all_by_department';

            return Cache::remember($cacheKey, 3600, function () use ($department) {
                $query = static::query()
                    ->where('is_active', true)
                    ->orderBy('name');

                if ($department) {
                    $query->where('department', $department);
                }

                $doctors = $query->get();

                if ($department) {
                    return $doctors->pluck('name')->toArray();
                }

                return $doctors->groupBy('department')
                    ->map(fn ($docs) => $docs->pluck('name')->toArray())
                    ->toArray();
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Table doesn't exist yet - migrations not run
            // Return empty array to trigger config fallback
            return [];
        }
    }

    /**
     * Clear the doctors cache when model is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('doctors.all_by_department');
            Cache::flush(); // Clear all doctors.department.* keys
        });

        static::deleted(function () {
            Cache::forget('doctors.all_by_department');
            Cache::flush();
        });
    }

    /**
     * Scope to get only active doctors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by department.
     */
    public function scopeForDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }
}
