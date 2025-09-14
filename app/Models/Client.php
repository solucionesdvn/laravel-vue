<?php

namespace App\Models;

use App\Models\Traits\ForCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Client extends Model
{
    use HasFactory, SoftDeletes, ForCompany;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'identification',
        'address',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


}
