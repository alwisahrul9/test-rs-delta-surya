<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Phone;
use App\Models\DoctorProfile;
use App\Models\PharmacistProfile;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    /**
     * Get the doctorProfile associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function doctorProfile(): HasOne
    {
        return $this->hasOne(DoctorProfile::class, 'user_id');
    }

    /**
     * Get the pharmacistProfile associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pharmacistProfile(): HasOne
    {
        return $this->hasOne(PharmacistProfile::class, 'user_id');
    }

    /**
     * Get all of the examination for the Patient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examinations(): HasMany
    {
        return $this->hasMany(Examinations::class, 'user_id');
    }

    /**
     * Get all of the prescription for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescriptions::class, 'user_id');
    }
}
