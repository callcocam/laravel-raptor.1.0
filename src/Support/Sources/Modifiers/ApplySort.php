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
 * Aplica ordenação. Suporta colunas diretas e colunas de relacionamento.
 *
 * Ex: ?sort=name&sort_dir=desc → orderBy('name', 'desc')
 *     ?sort=user.name&sort_dir=asc → orderBy via join/subquery no relationship
 */
class ApplySort implements QueryModifier
{
    public function apply(Builder $query, Request $request, TableQueryContext $context): Builder
    {
        $sortName = $request->get('sort');
        $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        if ($sortName === null || $sortName === '') {
            return $query;
        }

        foreach ($context->getSortableColumns() as $column) {
            if ($column->getName() !== $sortName) {
                continue;
            }

            if (method_exists($column, 'isRelationship') && $column->isRelationship()) {
                $this->sortByRelationship($query, $column, $sortDir);
            } else {
                $dbColumn = $column->getSortColumn();
                $query->orderBy($dbColumn, $sortDir);
            }

            break;
        }

        return $query;
    }

    /**
     * Ordenação por coluna de relacionamento usando subquery.
     * Ex: TextColumn::make('user.name')->sortable() → orderByRelationship
     */
    protected function sortByRelationship(Builder $query, mixed $column, string $direction): void
    {
        $relationPath = $column->getRelationshipPath();
        $relationColumn = $column->getRelationshipColumn();

        if ($relationPath === null || $relationColumn === null) {
            return;
        }

        $parts = explode('.', $relationPath);
        $model = $query->getModel();
        $currentModel = $model;

        foreach ($parts as $part) {
            if (! method_exists($currentModel, $part)) {
                return;
            }
            $relation = $currentModel->{$part}();
            $currentModel = $relation->getRelated();
        }

        $relatedTable = $currentModel->getTable();
        $relation = $model->{$parts[0]}();

        if (method_exists($relation, 'getForeignKeyName')) {
            $foreignKey = $relation->getForeignKeyName();
            $ownerKey = $relation->getOwnerKeyName() ?? $currentModel->getKeyName();

            $query->orderBy(
                $currentModel->newQuery()
                    ->select($relationColumn)
                    ->whereColumn(
                        "{$relatedTable}.{$ownerKey}",
                        "{$model->getTable()}.{$foreignKey}"
                    )
                    ->limit(1),
                $direction
            );
        }
    }
}
