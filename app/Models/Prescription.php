<?php

namespace App\Models;

use App\Models\Examination;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
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
                    default => auth()->user()->hasRole('pharmacist') ? "Status pembayaran resep telah di-{$eventName} oleh " . auth()->user()->name : "Resep telah di-{$eventName} oleh " . auth()->user()->name,
                };
            });
    }

    /**
     * Get the examination that owns the Prescriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class, 'examination_id');
    }

    /**
     * Get all of the prescriptionDetail for the Prescriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prescriptionDetails(): HasMany
    {
        return $this->hasMany(PrescriptionDetail::class, 'prescription_id');
    }

    /**
     * Get the user that owns the Prescriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
