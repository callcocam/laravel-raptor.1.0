<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types\Editable;

use Callcocam\LaravelRaptor\Support\Table\Columns\Column;
use Closure;
use Illuminate\Database\Eloquent\Model;

/**
 * Classe base para colunas editáveis inline
 */
abstract class BaseEditableColumn extends Column
{
    protected Closure|string|null $component = 'editable-table-column';

    protected ?Closure $updateCallback = null;

    protected array|Closure|null $rules = null;

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
            $this->rules = static fn () => $rules;
        } else {
            $this->rules = $rules;
        }

        return $this;
    }

    /**
     * Obtém o tipo de input específico (sobrescrito pelas subclasses)
     */
    abstract public function getInputType(): string;

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
        $this->evaluate($this->updateCallback, [
            'model' => $model,
            'value' => $value,
            'request' => $request,
        ]);

        return [

        ];
    }

    public function render(mixed $value, $row = null): mixed
    {
        return $this->getFormattedValue($value, $row);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'inputType' => $this->getInputType(),
            'rules' => $this->getValidationRules(),
        ]);
    }
}
