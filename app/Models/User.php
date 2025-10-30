<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nik',
        'phone',
        'gender',
        'date_of_birth',
        'license_number',
        'professional_identifier',
        'department',
        'designation',
        'last_login_at',
        'email_verified_at',
        'profile_meta',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
            'profile_meta' => 'array',
        ];
    }

    public function visitsAsProvider()
    {
        return $this->hasMany(Visit::class, 'provider_id');
    }

    public function labOrdersRequested()
    {
        return $this->hasMany(LabOrder::class, 'ordered_by');
    }

    public function labOrdersVerified()
    {
        return $this->hasMany(LabOrder::class, 'verified_by');
    }

    public function bpjsClaims()
    {
        return $this->hasMany(BpjsClaim::class, 'performed_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
