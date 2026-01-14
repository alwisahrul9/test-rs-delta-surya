<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use SoftDeletes, HasUuids, LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logAll() // Mencatat semua kolom
            ->logOnlyDirty() // Hanya mencatat kolom yang berubah saja
            ->setDescriptionForEvent(function(string $eventName) {
                return match ($eventName) {
                    'deleted' => "dipindahkan ke sampah (Soft Delete)",
                    'restored' => "dipulihkan kembali",
                    'forceDeleted' => "dihapus secara permanen",
                    default => "Pasien telah di-{$eventName} oleh " . auth()->user()->name,
                };
            });
    }

    /**
     * Get all of the examination for the Patient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examinations(): HasMany
    {
        return $this->hasMany(Examination::class, 'patient_id');
    }
}
