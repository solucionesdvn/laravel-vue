<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'company_id',
        'color',
    ];
    

    /**
     * Relación con la empresa (Company)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    // app/Models/Category.php

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}

