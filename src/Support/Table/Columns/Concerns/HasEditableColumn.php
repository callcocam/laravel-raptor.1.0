<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait HasEditableColumn
{
    protected ?Closure $updateCallback = null;

    protected array|Closure|null $rules = null;

    public function editable(Closure|string|null $component = 'editable-text-input')
    {
        $this->component($component);

        return $this;
    }

    /**
     * Define a callback de atualização
     * Recebe (Model $model, $value, Request $request)
     */
    public function updateUsing(Closure $callback): static
    {
        $this->updateCallback = $callback;

        return $this;
    }

    /**
     * Define regras de validação
     */
    public function rules(array|Closure $rules): static
    {
        if (is_array($rules)) {
            $this->rules = static fn() => $rules;
        } else {
            $this->rules = $rules;
        }

        return $this;
    }

    /**
     * Obtém as regras de validação
     */
    public function getValidationRules(): array
    {
        if ($this->rules === null) {
            return ['nullable'];
        }

        return (array) $this->evaluate($this->rules);
    }

    /**
     * Executa a atualização
     */
    public function update(Model $model, mixed $value, mixed $request = null): mixed
    {
        if ($this->updateCallback === null) {
            return null;
        }

        return $this->evaluate($this->updateCallback, [
            'model' => $model,
            'value' => $value,
            'request' => $request,
        ]);
    }
}
