<?php

namespace App\Models;

use App\Models\Traits\ForCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes, ForCompany;

    protected $appends = ['image_url'];

    // Habilita asignación masiva
    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'supplier_id',
        'stock',
        'price',
        'cost_price',
        'image',
    ];
    
    // Accesor para obtener la URL completa de la imagen
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

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
