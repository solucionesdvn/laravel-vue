<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormDefinition extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'fields', // JSON column
    ];

    protected $casts = [
        'fields' => 'array', // Cast JSON column to array
    ];

    /**
     * Get the company that owns the form definition.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the form data instances for the form definition.
     */
    public function formData(): HasMany
    {
        return $this->hasMany(FormData::class);
    }
}
