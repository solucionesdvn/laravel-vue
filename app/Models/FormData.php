<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormData extends Model
{
    protected $fillable = [
        'company_id',
        'form_definition_id',
        'data', // JSON column
        'token',
    ];

    protected $casts = [
        'data' => 'array', // Cast JSON column to array
    ];

    /**
     * Get the company that owns the form data.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the form definition that owns the form data.
     */
    public function formDefinition(): BelongsTo
    {
        return $this->belongsTo(FormDefinition::class);
    }
}
