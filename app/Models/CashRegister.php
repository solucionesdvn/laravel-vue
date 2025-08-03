<?php

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
        'opening_amount',   // <- corregido: era "opening_balance"
        'closing_amount',   // <- corregido: era "closing_balance"
        'total_sales',
        'total_expenses',   // <- agregado si vas a registrar egresos
        'status',           // <- nuevo campo para manejar estado
        'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_amount' => 'decimal:2',
        'closing_amount' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_expenses' => 'decimal:2',
    ];

    // Relaciones
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