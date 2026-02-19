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
 * Busca global. Suporta colunas diretas e de relacionamento.
 *
 * Ex: ?search=joao
 *   - TextColumn::make('name')->searchable() → where name LIKE %joao%
 *   - TextColumn::make('user.name')->searchable() → whereHas('user', fn => where name LIKE %joao%)
 */
class ApplySearch implements QueryModifier
{
    public function apply(Builder $query, Request $request, TableQueryContext $context): Builder
    {
        $search = $request->input('search');
        if ($search === null || $search === '') {
            return $query;
        }

        $searchable = $context->getSearchableColumns();
        if (count($searchable) === 0) {
            return $query;
        }

        $query->where(function (Builder $q) use ($searchable, $search) {
            foreach ($searchable as $column) {
                if (method_exists($column, 'isRelationship') && $column->isRelationship()) {
                    $this->searchInRelationship($q, $column, $search);
                } else {
                    $q->orWhere($column->getName(), 'like', '%'.$search.'%');
                }
            }
        });

        return $query;
    }

    protected function searchInRelationship(Builder $query, mixed $column, string $search): void
    {
        $relationPath = $column->getRelationshipPath();
        $relationColumn = $column->getRelationshipColumn();

        if ($relationPath === null || $relationColumn === null) {
            return;
        }

        $query->orWhereHas($relationPath, function (Builder $q) use ($relationColumn, $search) {
            $q->where($relationColumn, 'like', '%'.$search.'%');
        });
    }
}
