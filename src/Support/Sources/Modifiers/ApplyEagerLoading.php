<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Sources\Modifiers;

use Callcocam\LaravelRaptor\Support\Table\TableQueryContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Detecta colunas com relationship (dot notation ou explícito) e
 * adiciona eager loading para evitar N+1.
 *
 * Ex: TextColumn::make('user.name') → $query->with('user')
 *     TextColumn::make('category.parent.title') → $query->with('category.parent')
 */
class ApplyEagerLoading implements QueryModifier
{
    public function apply(Builder $query, Request $request, TableQueryContext $context): Builder
    {
        $relationships = $context->getRelationships();

        if (count($relationships) > 0) {
            $query->with($relationships);
        }

        return $query;
    }
}
