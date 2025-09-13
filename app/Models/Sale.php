<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Client;

class Sale extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'company_id',
        'user_id',
        'cash_register_id',
        'client_id',
        'payment_method_id',
        'date',
        'total',
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

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted()
    {
        static::deleting(function ($sale) {
            if ($sale->isForceDeleting()) {
                // Lógica para borrado permanente si es necesario
            } else {
                // Borrado suave en cascada para los items
                $sale->items()->delete();
            }
        });
    }

    /**
     * Verifica si la venta puede ser anulada.
     * Una venta no es anulable si su caja registradora ya ha sido cerrada.
     *
     * @return bool
     */
    public function isAnnullable(): bool
    {
        return is_null($this->cashRegister?->closed_at);
    }
}