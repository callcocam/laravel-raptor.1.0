<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Concerns\EvaluatesClosures;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithColumns;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithFilters;
use Callcocam\LaravelRaptor\Support\Sources\DatabaseSource;
use Callcocam\LaravelRaptor\Support\Sources\SourceContract;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TableBuilder
{
    use EvaluatesClosures;
    use WithColumns;
    use WithFilters;

    /**
     * @var Closure|array<int, AbstractColumn>
     */
    protected Closure|array $columns = [];

    protected ?SourceContract $source = null;

    public function __construct(protected Request $request, protected ?Model $model = null) {}

    public function source(SourceContract $source): static
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Payload pronto para o frontend (Vue, React, Livewire, Blade).
     * Dados já tratados no backend: colunas serializadas + linhas com valores formatados + meta.
     */
    public function render(): array
    {
        $resolvedColumns = $this->getResolvedColumns();
        $context = new TableQueryContext($resolvedColumns, $this->getResolvedFilters());

        $source = $this->getSource();
        $data = $source->getData($this->request, $context);

        $items = $data instanceof LengthAwarePaginator
            ? $data->items()
            : $data['data'] ?? [];
        $meta = $data instanceof LengthAwarePaginator
            ? $this->paginatorMeta($data)
            : ($data['meta'] ?? ['total' => $data['total'] ?? 0]);

        $rows = [];
        foreach ($items as $row) {
            $rows[] = $this->buildRow($row, $resolvedColumns);
        }

        return [
            'columns' => array_map(fn (AbstractColumn $col) => $col->toArray(), $resolvedColumns),
            'data' => $rows,
            'meta' => $meta,
        ];
    }

    protected function getSource(): SourceContract
    {
        if ($this->source !== null) {
            return $this->source;
        }

        $model = $this->model;
        $source = new DatabaseSource($model);
        $this->source = $source;

        return $source;
    }

    /**
     * @return array<int, AbstractColumn>
     */
    protected function getResolvedColumns(): array
    {
        $evaluated = $this->getColumns($this->model);
        if (! is_array($evaluated)) {
            return [];
        }

        return array_values($evaluated);
    }

    /**
     * @return array<int, FilterBuilder>
     */
    protected function getResolvedFilters(): array
    {
        $evaluated = $this->getFilters();
        if (! is_array($evaluated)) {
            return [];
        }

        return array_values($evaluated);
    }

    /**
     * @param  array<int, AbstractColumn>  $columns
     * @return array<string, mixed>
     */
    protected function buildRow(Model|array $row, array $columns): array
    {
        $out = [];

        foreach ($columns as $column) {
            $name = $column->getName();
            $value = $this->resolveValue($row, $column);
            $out[$name] = $column->render($value, $row);
        }

        if ($row instanceof Model && $row->getKey()) {
            $out['id'] = $row->getKey();
        }

        return $out;
    }

    /**
     * Resolve o valor da coluna, incluindo dot notation para relationships.
     * Ex: "user.name" → $row->user->name (eager loaded)
     */
    protected function resolveValue(Model|array $row, AbstractColumn $column): mixed
    {
        if (method_exists($column, 'isRelationship') && $column->isRelationship()) {
            $path = $column->getRelationshipPath();
            $field = $column->getRelationshipColumn();

            if ($row instanceof Model) {
                $related = data_get($row, $path);

                return $related !== null ? data_get($related, $field) : null;
            }

            return data_get($row, $column->getName());
        }

        if ($row instanceof Model) {
            return $row->getAttribute($column->getName());
        }

        return data_get($row, $column->getName());
    }

    /**
     * @return array<string, mixed>
     */
    protected function paginatorMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }
}
