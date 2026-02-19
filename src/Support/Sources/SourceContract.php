<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Sources;

use Callcocam\LaravelRaptor\Support\Table\Summarizers\Summarizer;
use Callcocam\LaravelRaptor\Support\Table\TableQueryContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * Contrato para fontes de dados da tabela (banco, Excel, API).
 * Todas as fontes retornam dados em estrutura normalizada para o backend
 * processar e enviar um payload pronto para qualquer front (Vue, React, Livewire, Blade).
 * O contexto (colunas + filtros) é opcional; DatabaseSource usa para ordenação, busca e filtros.
 */
interface SourceContract
{
    /**
     * Retorna os dados da fonte (paginação, filtros já aplicados).
     * O contexto traz colunas (sortable/searchable) e filtros para a fonte aplicar na query.
     */
    public function getData(Request $request, ?TableQueryContext $context = null): LengthAwarePaginator|array;

    /**
     * Retorna o total de registros (sem paginação), para meta/count.
     * Com contexto, o total considera os mesmos filtros/busca (ex.: DatabaseSource).
     */
    public function getTotal(Request $request, ?TableQueryContext $context = null): int;

    /**
     * Calcula aggregates globais (sobre todos os registros filtrados, não só a página).
     *
     * @param  array<int, Summarizer>  $summarizers
     * @return array<string, mixed>
     */
    public function getSummary(Request $request, ?TableQueryContext $context, array $summarizers): array;
}
