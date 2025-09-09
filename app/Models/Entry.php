<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    protected $fillable = [
        'company_id',
        'supplier_id',
        'date',
        'total_cost',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(EntryItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
