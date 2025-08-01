<?php
// app/Models/CashRegister.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'opened_at',
        'closed_at',
        'opening_balance',
        'closing_balance',
        'total_sales',
        'notes',
    ];

    protected $dates = ['opened_at', 'closed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
