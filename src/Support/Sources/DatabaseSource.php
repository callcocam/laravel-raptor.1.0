<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Sources;

use Callcocam\LaravelRaptor\Support\Sources\Modifiers\ApplyEagerLoading;
use Callcocam\LaravelRaptor\Support\Sources\Modifiers\ApplyFilters;
use Callcocam\LaravelRaptor\Support\Sources\Modifiers\ApplySearch;
use Callcocam\LaravelRaptor\Support\Sources\Modifiers\ApplySort;
use Callcocam\LaravelRaptor\Support\Sources\Modifiers\QueryModifier;
use Callcocam\LaravelRaptor\Support\Table\TableQueryContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Fonte padrão: dados vindos do banco via Eloquent.
 * Usa um pipeline de QueryModifiers (cada etapa isolada).
 */
class DatabaseSource extends AbstractSource
{
    public function __construct(?\Illuminate\Database\Eloquent\Model $model = null, protected ?\Closure $queryCallback = null)
    {
        parent::__construct($model);
    }

    /**
     * Pipeline de modifiers padrão. Sobrescreva para adicionar/remover etapas.
     *
     * @return array<int, QueryModifier>
     */
    protected function getModifiers(): array
    {
        return array_merge([
            new ApplyEagerLoading,
            new ApplySearch,
            new ApplyFilters,
            new ApplySort,
        ], config('raptor.sources.database.modifiers', []));
    }

    public function getData(Request $request, ?TableQueryContext $context = null): LengthAwarePaginator|array
    {
        if ($this->model === null) {
            return $this->emptyResult();
        }

        $query = $this->buildQuery($request, $context);

        $perPage = (int) $request->input('per_page', 15);
        $perPage = $perPage >= 1 && $perPage <= 100 ? $perPage : 15;

        return $query->paginate($perPage);
    }

    public function getTotal(Request $request, ?TableQueryContext $context = null): int
    {
        if ($this->model === null) {
            return 0;
        }

        return $this->buildQuery($request, $context)->count();
    }

    /**
     * Monta a query passando por todos os modifiers do pipeline.
     */
    protected function buildQuery(Request $request, ?TableQueryContext $context): Builder
    {
        $query = $this->baseQuery();

        if ($context !== null) {
            foreach ($this->getModifiers() as $modifier) {
                $query = $modifier->apply($query, $request, $context);
            }
        }

        if ($this->queryCallback !== null) {
            $query = $this->evaluate($this->queryCallback, ['query' => $query]) ?? $query;
        }

        return $query;
    }

    /**
     * Query base antes do pipeline. Sobrescreva para scopes globais.
     */
    protected function baseQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Calcula aggregates globais (sobre TODOS os registros filtrados, não só a página).
     *
     * @param  array<int, \Callcocam\LaravelRaptor\Support\Table\Summarizers\Summarizer>  $summarizers
     * @return array<string, mixed>
     */
    public function getSummary(Request $request, ?TableQueryContext $context, array $summarizers): array
    {
        if ($this->model === null || count($summarizers) === 0) {
            return [];
        }

        $query = $this->buildQuery($request, $context);
        $results = [];

        foreach ($summarizers as $summarizer) {
            $key = $summarizer->getFunction().'_'.$summarizer->getColumn();
            $results[$key] = [
                'value' => $summarizer->computeFromQuery(clone $query),
                'label' => $summarizer->getLabel(),
                'column' => $summarizer->getColumn(),
                'function' => $summarizer->getFunction(),
                'prefix' => method_exists($summarizer, 'getPrefix') ? $summarizer->getPrefix() : null,
                'suffix' => method_exists($summarizer, 'getSuffix') ? $summarizer->getSuffix() : null,
            ];
        }

        return $results;
    }

    /**
     * @return array{data: array, total: int, meta: array{current_page: int, per_page: int, last_page: int}}
     */
    protected function emptyResult(): array
    {
        return [
            'data' => [],
            'total' => 0,
            'meta' => [
                'current_page' => 1,
                'per_page' => 15,
                'last_page' => 1,
            ],
        ];
    }
}
