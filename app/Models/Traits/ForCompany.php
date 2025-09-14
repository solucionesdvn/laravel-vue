<?php

namespace App\Models\Traits;

use App\Models\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;

trait ForCompany
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Aplica el scope global para filtrar por compañía
        static::addGlobalScope(new CompanyScope());

        // Asigna el company_id automáticamente al crear un nuevo modelo
        static::creating(function ($model) {
            if (Auth::hasUser()) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }
}
