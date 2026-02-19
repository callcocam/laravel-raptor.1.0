<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Sources;

use Callcocam\LaravelRaptor\Support\Concerns\EvaluatesClosures;
use Callcocam\LaravelRaptor\Support\Concerns\FactoryPattern;
use Callcocam\LaravelRaptor\Support\Table\TableQueryContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Fonte abstrata de dados para tabelas.
 * Padrão: banco (Eloquent). Pode ser estendida para Excel, API, etc.
 * Todas as fontes normalizam os dados no backend antes de enviar ao front.
 */
abstract class AbstractSource implements SourceContract
{
    use EvaluatesClosures;
    use FactoryPattern;

    public function __construct(protected ?Model $model = null) {}

    /**
     * Retorna os dados da fonte. Subclasses implementam (DB, Excel, API).
     * O contexto (colunas + filtros) é usado pela DatabaseSource para ordenação e filtros.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|array{data: array, total: int, meta: array}
     */
    abstract public function getData(Request $request, ?TableQueryContext $context = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator|array;

    /**
     * Retorna o total de registros. Subclasses implementam.
     * O contexto permite aplicar os mesmos filtros/busca ao count (DatabaseSource).
     */
    abstract public function getTotal(Request $request, ?TableQueryContext $context = null): int;

    /**
     * Calcula aggregates globais. Por padrão retorna vazio; DatabaseSource sobrescreve.
     *
     * @param  array<int, \Callcocam\LaravelRaptor\Support\Table\Summarizers\Summarizer>  $summarizers
     * @return array<string, mixed>
     */
    public function getSummary(Request $request, ?TableQueryContext $context, array $summarizers): array
    {
        return [];
    }
}
