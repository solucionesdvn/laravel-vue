<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Habilita asignación masiva
    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'supplier_id',
        'stock',
        'price',
        'image',
        'company_id',
    ];
    

    // Relaciones
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function entryItems()
    {
        return $this->hasMany(EntryItem::class);
    }

}
