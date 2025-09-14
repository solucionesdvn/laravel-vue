<?php

namespace App\Models;

use App\Models\Traits\ForCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResignationForm extends Model
{
    use HasFactory, ForCompany;

    protected $fillable = [
        // 'company_id', // Handled by ForCompany trait
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
