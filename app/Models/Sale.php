<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;

class Sale extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'cash_register_id',
        'date',
        'total',
        'payment_method',
    ];

    protected $casts = [
        'date' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}