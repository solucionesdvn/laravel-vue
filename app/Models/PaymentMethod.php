<?php

namespace App\Models;

use App\Models\Traits\ForCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes, ForCompany;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the company that owns the payment method.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
