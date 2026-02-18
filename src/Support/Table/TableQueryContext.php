<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table;

use Callcocam\LaravelRaptor\Support\AbstractColumn;

/**
 * Contexto passado da TableBuilder para a Source.
 * Centraliza acesso a colunas, filtros, relationships, sortable, searchable.
 */
class TableQueryContext
{
    /**
     * @param  array<int, AbstractColumn>  $columns
     * @param  array<int, FilterBuilder>  $filters
     */
    public function __construct(
        protected array $columns = [],
        protected array $filters = []
    ) {}

    /**
     * @return array<int, AbstractColumn>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array<int, FilterBuilder>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Colunas que podem ser ordenadas.
     *
     * @return array<int, AbstractColumn>
     */
    public function getSortableColumns(): array
    {
        return array_filter($this->columns, function ($column) {
            return method_exists($column, 'isSortable') && $column->isSortable();
        });
    }

    /**
     * Colunas que entram na busca global.
     *
     * @return array<int, AbstractColumn>
     */
    public function getSearchableColumns(): array
    {
        return array_filter($this->columns, function ($column) {
            return method_exists($column, 'isSearchable') && $column->isSearchable();
        });
    }

    /**
     * Relationships únicos para eager loading (de colunas com dot notation).
     * Ex: ['user', 'category.parent']
     *
     * @return array<int, string>
     */
    public function getRelationships(): array
    {
        $relationships = [];

        foreach ($this->columns as $column) {
            if (! method_exists($column, 'isRelationship') || ! $column->isRelationship()) {
                continue;
            }
            $path = $column->getRelationshipPath();
            if ($path !== null && ! in_array($path, $relationships, true)) {
                $relationships[] = $path;
            }
        }

        return $relationships;
    }

    /**
     * Colunas que pertencem a um relacionamento.
     *
     * @return array<int, AbstractColumn>
     */
    public function getRelationshipColumns(): array
    {
        return array_filter($this->columns, function ($column) {
            return method_exists($column, 'isRelationship') && $column->isRelationship();
        });
    }
}
