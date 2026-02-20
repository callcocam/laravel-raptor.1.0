<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Types;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithColumns;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Modal com formulário.
 * Usa WithColumns para definir campos; onSubmit é executado no backend.
 */
class ModalFormAction extends ModalAction
{
    use WithColumns;

    protected Closure|string|null $component = 'modal-form-action'; 

    protected Closure|array $columns = [];

    protected ?Closure $onSubmitCallback = null;

    public function onSubmit(Closure $callback): static
    {
        $this->onSubmitCallback = $callback;

        return $this;
    }

    /**
     * Executar o submit (chamado pelo AbstractController::executeAction).
     */
    public function execute(Model $model, Request $request): mixed
    {
        if ($this->onSubmitCallback !== null) {
            $result = ($this->onSubmitCallback)($model, $request);

            if ($this->afterExecute !== null) {
                return ($this->afterExecute)($model, $request, $result);
            }

            if ($result instanceof RedirectResponse) {
                return $result;
            }

            return $result;
        }

        return parent::execute($model, $request);
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
        ]);
    }
}
