<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'company_id',
        'color',
    ];
    

    /**
     * Relación con la empresa (Company)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    
}

