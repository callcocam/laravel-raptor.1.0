<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Sources;

use Callcocam\LaravelRaptor\Support\Table\TableQueryContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * Fonte de dados via API externa.
 * Implementar getData(), getTotal(), fetchFromApi() e normalizeApiItems().
 */
abstract class ApiSource extends AbstractSource
{
    public function getData(Request $request, ?TableQueryContext $context = null): LengthAwarePaginator|array
    {
        $response = $this->fetchFromApi($request);
        $items = $this->normalizeApiItems($response);

        $total = $this->getTotal($request);
        $perPage = (int) $request->get('per_page', 15);
        $page = (int) $request->get('page', 1);

        return [
            'data' => $items,
            'total' => $total,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => $perPage > 0 ? (int) ceil($total / $perPage) : 1,
            ],
        ];
    }

    /**
     * Chamada à API. Subclasse implementa (HTTP client, endpoint, filtros).
     *
     * @return array<string, mixed>
     */
    abstract protected function fetchFromApi(Request $request): array;

    /**
     * Normaliza os itens da resposta da API para array de arrays/objetos.
     *
     * @param  array<string, mixed>  $response
     * @return array<int, array<string, mixed>>
     */
    abstract protected function normalizeApiItems(array $response): array;

    abstract public function getTotal(Request $request, ?TableQueryContext $context = null): int;
}
