<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Types;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithColumns;
use Callcocam\LaravelRaptor\Support\Sources\SourceContract;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Modal com listagem/tabela.
 * Usa WithColumns para definir colunas e uma SourceContract para os dados.
 */
class ModalTableAction extends ModalAction
{
    use WithColumns;

    protected Closure|array $columns = [];

    protected ?SourceContract $dataSource = null;

    protected ?Closure $dataCallback = null;

    /**
     * Source para carregar dados da tabela na modal.
     */
    public function dataSource(SourceContract $source): static
    {
        $this->dataSource = $source;

        return $this;
    }

    /**
     * Callback para carregar dados (alternativa à source).
     * Recebe (Model $model, Request $request) e retorna array de dados.
     */
    public function dataUsing(Closure $callback): static
    {
        $this->dataCallback = $callback;

        return $this;
    }

    /**
     * Executar carregamento de dados (chamado pelo AbstractController::executeAction).
     * Retorna JSON com dados e colunas para a modal.
     */
    public function execute(Model $model, Request $request): mixed
    {
        $resolvedColumns = $this->getColumns($model);
        $columnsArray = is_array($resolvedColumns)
            ? array_map(fn (AbstractColumn $col) => $col->toArray(), $resolvedColumns)
            : [];

        $data = [];
        if ($this->dataCallback !== null) {
            $data = ($this->dataCallback)($model, $request);
        } elseif ($this->dataSource !== null) {
            $result = $this->dataSource->getData($request);
            $data = method_exists($result, 'items') ? $result->items() : ($result['data'] ?? []);
        }

        return response()->json([
            'columns' => $columnsArray,
            'data' => $data,
        ]);
    }

    public function render(?Model $model = null, ?Request $request = null): ?array
    {
        $base = parent::render($model, $request);
        if ($base === null) {
            return null;
        }

        $resolvedColumns = $this->getColumns($model);
        $columnsArray = is_array($resolvedColumns)
            ? array_map(fn (AbstractColumn $col) => $col->toArray(), $resolvedColumns)
            : [];

        return array_merge($base, [
            'columns' => $columnsArray,
            'dataUrl' => $this->resolveExecuteUrl($model, $request),
        ]);
    }
}
