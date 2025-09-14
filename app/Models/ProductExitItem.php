<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExitItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_exit_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function productExit()
    {
        return $this->belongsTo(ProductExit::class);
    }
}