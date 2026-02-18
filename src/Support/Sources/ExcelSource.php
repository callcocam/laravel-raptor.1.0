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
 * Fonte de dados a partir de arquivo Excel.
 * Implementar getData() e getTotal() lendo o arquivo (e.g. Laravel Excel / PhpSpreadsheet).
 */
abstract class ExcelSource extends AbstractSource
{
    public function getData(Request $request, ?TableQueryContext $context = null): LengthAwarePaginator|array
    {
        $items = $this->readExcelRows($request);
        $total = count($items);
        $perPage = (int) $request->get('per_page', 15);
        $page = (int) $request->get('page', 1);
        $perPage = $perPage >= 1 && $perPage <= 100 ? $perPage : 15;
        $offset = ($page - 1) * $perPage;
        $sliced = array_slice($items, $offset, $perPage);

        return [
            'data' => $sliced,
            'total' => $total,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => $perPage > 0 ? (int) ceil($total / $perPage) : 1,
            ],
        ];
    }

    public function getTotal(Request $request): int
    {
        return count($this->readExcelRows($request));
    }

    /**
     * Lê as linhas do Excel. Subclasse implementa (upload, path, sheet).
     *
     * @return array<int, array<string, mixed>>
     */
    abstract protected function readExcelRows(Request $request): array;
}
