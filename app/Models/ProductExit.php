<?php

namespace App\Models;

use App\Models\Traits\ForCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExit extends Model
{
    use HasFactory, ForCompany;

    protected $fillable = [
        // 'company_id', // Handled by ForCompany trait
        'user_id',
        'date',
        'reason',
        'notes',
        'total',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(ProductExitItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
