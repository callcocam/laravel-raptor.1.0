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
    /**
     * Pipeline de modifiers padrão. Sobrescreva para adicionar/remover etapas.
     *
     * @return array<int, QueryModifier>
     */
    protected function getModifiers(): array
    {
        return [
            new ApplyEagerLoading,
            new ApplySearch,
            new ApplyFilters,
            new ApplySort,
        ];
    }

    public function getData(Request $request, ?TableQueryContext $context = null): LengthAwarePaginator|array
    {
        if ($this->model === null) {
            return $this->emptyResult();
        }

        $query = $this->buildQuery($request, $context);

        $perPage = (int) $request->get('per_page', 15);
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

        if ($context === null) {
            return $query;
        }

        foreach ($this->getModifiers() as $modifier) {
            $query = $modifier->apply($query, $request, $context);
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
