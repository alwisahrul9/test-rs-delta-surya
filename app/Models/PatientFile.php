<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientFile extends Model
{
    use SoftDeletes, HasUuids;

    protected $guarded = ['id'];

    /**
     * Get the examination that owns the PatientFile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examinations::class, 'user_id');
    }
}
