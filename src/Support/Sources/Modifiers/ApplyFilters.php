<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Sources\Modifiers;

use Callcocam\LaravelRaptor\Support\Table\FilterBuilder;
use Callcocam\LaravelRaptor\Support\Table\TableQueryContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Aplica filtros personalizados (FilterBuilder com queryUsing/strategy).
 */
class ApplyFilters implements QueryModifier
{
    public function apply(Builder $query, Request $request, TableQueryContext $context): Builder
    {
        foreach ($context->getFilters() as $filter) {
            if (! $filter instanceof FilterBuilder) {
                continue;
            }

            $value = $request->get($filter->getName());
            $query = $filter->applyToQuery($query, $value);
        }

        return $query;
    }
}
