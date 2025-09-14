<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Aplica el filtro solo si hay un usuario autenticado
        if (Auth::hasUser()) {
            $builder->where($model->getTable().'.company_id', Auth::user()->company_id);
        }
    }
}
