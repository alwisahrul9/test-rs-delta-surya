<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Examination extends Model
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
                    default => "Dokumen pemeriksaan telah di-{$eventName} oleh " . auth()->user()->name,
                };
            });
    }

    /**
     * Get the patient that owns the Examination
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the patient that owns the Examination
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the file for the Examination
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientFiles(): HasMany
    {
        return $this->hasMany(PatientFile::class, 'examination_id');
    }

    /**
     * Get the prescription associated with the Examination
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class, 'examination_id');
    }
}
