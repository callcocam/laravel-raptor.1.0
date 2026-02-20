<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Actions\AbstractAction;
use Callcocam\LaravelRaptor\Support\Concerns\EvaluatesClosures;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithActions;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithBulkActions;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithColumns;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithFilters;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithHeaderActions;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithSummarizers;
use Callcocam\LaravelRaptor\Support\Sources\DatabaseSource;
use Callcocam\LaravelRaptor\Support\Sources\SourceContract;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TableBuilder
{
    use EvaluatesClosures;
    use WithActions;
    use WithBulkActions;
    use WithColumns;
    use WithFilters;
    use WithHeaderActions;
    use WithSummarizers;

    /**
     * @var Closure|array<int, AbstractColumn>
     */
    protected Closure|array $columns = [];

    /**
     * @var Closure|array<int, FilterBuilder>
     */
    protected Closure|array $filters = [];

    protected ?SourceContract $source = null;

    /** @var Closure|null Recebe (Builder) para customizar a query (ex.: eager load). */
    protected ?Closure $queryCallback = null;

    protected bool $selectable = false;

    /** @var Closure|null Se null, usa Model::getKey() como valor de seleção. */
    protected ?Closure $selectableKeyResolver = null;

    protected ?string $defaultSortColumn = null;

    protected string $defaultSortDirection = 'asc';

    public function __construct(protected Request $request, protected ?Model $model = null) {}

    /**
     * Define ordenação padrão quando a request não envia sort/sort_dir.
     */
    public function defaultSort(string $column, string $direction = 'asc'): static
    {
        $this->defaultSortColumn = $column;
        $this->defaultSortDirection = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return $this;
    }

    /**
     * Customiza a query (ex.: eager load). Closure recebe o Builder.
     *
     * @param  Closure(\Illuminate\Database\Eloquent\Builder): (\Illuminate\Database\Eloquent\Builder|void)  $callback
     */
    public function queryUsing(Closure $callback): static
    {
        $this->queryCallback = $callback;

        return $this;
    }

    /**
     * Habilita checkboxes por linha e "selecionar tudo".
     * Passar true para usar o id do model, ou Closure (Model $model) => value para chave customizada.
     *
     * @param  bool|Closure(Model): (string|int)  $value
     */
    public function selectable(bool|Closure $value = true): static
    {
        $this->selectable = $value !== false;

        if ($value instanceof Closure) {
            $this->selectableKeyResolver = $value;
        } else {
            $this->selectableKeyResolver = null;
        }

        return $this;
    }

    public function isSelectable(): bool
    {
        return $this->selectable;
    }

    public function source(SourceContract $source): static
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Payload pronto para o frontend (Vue, React, Livewire, Blade).
     *
     * - columns: metadata das colunas (uma vez só)
     * - data: linhas com apenas valores formatados (leve)
     * - meta: paginação
     */
    public function render(): array
    {
        $resolvedColumns = $this->getResolvedColumns();
        $context = new TableQueryContext(
            $resolvedColumns,
            $this->getResolvedFilters(),
            $this->defaultSortColumn,
            $this->defaultSortDirection
        );

        $source = $this->getSource();
        $result = $source->getData($this->request, $context);

        if ($result instanceof LengthAwarePaginator) {
            $items = $result->items();
            $meta = $this->paginatorMeta($result);
        } else {
            $items = $result['data'] ?? [];
            $meta = $result['meta'] ?? ['total' => $result['total'] ?? 0];
        }

        $rows = array_map(
            fn ($item) => $this->buildRow($item, $resolvedColumns),
            $items
        );

        $payload = [
            'columns' => array_map(fn (AbstractColumn $col) => $col->toArray(), $resolvedColumns),
            'data' => $rows,
            'meta' => $meta,
        ];

        if ($this->selectable) {
            $payload['selectable'] = true;
        }

        if ($this->hasActions()) {
            $payload['actions'] = array_map(
                fn (AbstractAction $action) => $action->toArray(),
                $this->getActions()
            );
        }

        if ($this->hasHeaderActions()) {
            $payload['headerActions'] = $this->renderStaticActions($this->getHeaderActions());
        }

        if ($this->hasBulkActions()) {
            $payload['bulkActions'] = $this->renderStaticActions($this->getBulkActions());
        }

        if ($this->hasSummarizers()) {
            $payload['summary'] = $this->buildSummary($source, $context, $rows);
        }
        Storage::put('payload.json', json_encode($payload, JSON_PRETTY_PRINT));

        return $payload;
    }

    protected function getSource(): SourceContract
    {
        if ($this->source !== null) {
            return $this->source;
        }

        $model = $this->model;
        $source = new DatabaseSource($model, $this->queryCallback);
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
     * Calcula os summaries da página e o total geral.
     *
     * @param  array<int, array<string, mixed>>  $rows  Linhas já formatadas da página
     * @return array{page: array, global: array}
     */
    protected function buildSummary(SourceContract $source, TableQueryContext $context, array $rows): array
    {
        $summarizers = $this->getSummarizers();

        $pageSummary = [];
        foreach ($summarizers as $summarizer) {
            $key = $summarizer->getFunction().'_'.$summarizer->getColumn();
            $pageSummary[$key] = [
                'value' => $summarizer->computeFromRows($rows),
                'label' => $summarizer->getLabel(),
                'column' => $summarizer->getColumn(),
                'function' => $summarizer->getFunction(),
            ];
        }

        $globalSummary = $source->getSummary($this->request, $context, $summarizers);

        return [
            'page' => $pageSummary,
            'global' => $globalSummary,
        ];
    }

    /**
     * Monta uma linha com só os valores formatados + id + actions.
     * Sem metadata de colunas — o front cruza com `columns` pelo name.
     *
     * @param  array<int, AbstractColumn>  $columns
     * @return array<string, mixed>
     */
    protected function buildRow(mixed $item, array $columns): array
    {
        $row = [];

        if ($item instanceof Model && $item->getKey()) {
            $row['id'] = $item->getKey();
        }

        if ($this->selectable && $item instanceof Model) {
            $row['_selectId'] = $this->selectableKeyResolver
                ? $this->evaluate($this->selectableKeyResolver, ['model' => $item])
                : $item->getKey();
        }

        foreach ($columns as $column) {
            $name = $column->getName();
            $value = $this->resolveValue($item, $column);
            $row[$name] = $column->render($value, $item);
        }

        if ($item instanceof Model) {
            $row['actions'] = $this->evaluateActionsAuthorization($item);
        }

        return $row;
    }

    /**
     * Resolve o valor incluindo dot notation para relationships.
     */
    protected function resolveValue(mixed $item, AbstractColumn $column): mixed
    {
        if (method_exists($column, 'isRelationship') && $column->isRelationship()) {
            $path = $column->getRelationshipPath();
            $field = $column->getRelationshipColumn();

            if ($item instanceof Model) {
                $related = data_get($item, $path);

                return $related !== null ? data_get($related, $field) : null;
            }
        }

        if ($item instanceof Model) {
            return $item->getAttribute($column->getName());
        }

        return data_get($item, $column->getName());
    }

    /**
     * Renderiza actions estáticas (header/bulk) que não dependem de um registro.
     *
     * @param  AbstractAction[]  $actions
     * @return array<int, array<string, mixed>>
     */
    protected function renderStaticActions(array $actions): array
    {
        $rendered = [];

        foreach ($actions as $action) {
            $result = $action->render(null, $this->request);
            if ($result !== null) {
                $rendered[] = $result;
            }
        }

        return $rendered;
    }

    /**
     * Avalia quais actions o usuário pode executar neste registro.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function evaluateActionsAuthorization(Model $model): array
    {
        $actions = [];

        foreach ($this->getActions() as $action) {
            $rendered = $action->render($model, $this->request);
            if ($rendered !== null) {
                $actions[$action->getName()] = $rendered;
            }
        }

        return $actions;
    }

    /**
     * @return array<string, mixed>
     */
    protected function paginatorMeta(mixed $data): array
    {
        if ($data instanceof LengthAwarePaginator) {
            return [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ];
        }

        return $data['meta'] ?? ['total' => $data['total'] ?? 0];
    }
}
