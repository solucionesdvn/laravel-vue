<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryItem extends Model
{
    protected $fillable = [
        'entry_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
