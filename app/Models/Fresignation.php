<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fresignation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'city',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'reason',
        'company_id',
        'expires_at',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
