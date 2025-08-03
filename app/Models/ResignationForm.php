<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResignationForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'full_name',
        'resignation_date',
        'reason',
        'signature',
        'token',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}