<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form\Columns;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Concerns\HasGridLayout;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongToRequest;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToValidation;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class Column extends AbstractColumn
{
    use HasGridLayout;
    use BelongToRequest;
    use BelongsToValidation;
    /** @var array<int, string>|string|Closure|null */
    protected array|string|Closure|null $rules = null;

    protected Closure|string|null $placeholder = null;

    protected ?Closure $valueUsing = null;

    protected ?Closure $defaultUsing = null;



    protected function setUp(): void {}
    /**
     * Define uma closure para resolver o valor do campo.
     *
     * @param  Closure  $callback
     * @return static
     */
    public function valueUsing(Closure $callback): static
    {
        $this->valueUsing = $callback;

        return $this;
    }

    /**
     * Resolve o valor do campo.
     *
     * @param  Request  $request
     * @param  ?Model  $model
     * @return mixed
     */
    public function getValueUsing(?Request $request, ?Model $model = null): mixed
    {
        if ($this->valueUsing === null) {
            return null;
        }

        return $this->evaluate($this->valueUsing, [
            'request' => $request,
            'data' => $request,
            'model' => $model,
        ]);
    }

    /**
     * Define uma closure para resolver o valor padrão do campo.
     *
     * @param  Closure  $callback
     * @return static
     */
    public function defaultUsing(Closure $callback): static
    {
        $this->defaultUsing = $callback;

        return $this;
    }

    /**
     * Resolve o valor padrão do campo.
     *
     * @param  ?Request  $request
     * @param  Model|null  $model
     * @return mixed
     */
    public function getDefaultUsing(?Request $request, ?Model $model = null): mixed
    {
        if ($this->defaultUsing === null) {
            return null;
        }

        return $this->evaluate($this->defaultUsing, [
            'request' => $request,
            'data' => $request,
            'model' => $model,
        ]);
    }

    public function hasDefaultUsing(): bool
    {
        return $this->defaultUsing !== null;
    }

    /**
     * Valor deste campo para o payload do form. Cada field trata seus próprios dados.
     */
    public function getFormValues(?Model $model = null, ?Request $request = null): array
    {
        $value = $this->getValueUsing($request, $model);
        if ($value !== null) {
            return [$this->getName() => $value];
        }
        if ($model !== null) {
            $value = $model->getAttribute($this->getName());
            if ($value !== null) {
                return [$this->getName() => $value];
            }
        }
        if ($this->hasDefaultUsing()) {
            return [$this->getName() => $this->getDefaultUsing($request, $model)];
        }

        return [$this->getName() => null];
    }

    /**
     * @param  array<int, string>|string|Closure  $rules
     */
    public function rules(array|string|Closure $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return array<int, string>|null
     */
    public function getRules(): ?array
    {
        if ($this->rules === null) {
            return null;
        }

        $evaluated = $this->evaluate($this->rules);
        if (is_string($evaluated)) {
            return array_map('trim', explode('|', $evaluated));
        }

        return is_array($evaluated) ? $evaluated : null;
    }

    public function placeholder(Closure|string|null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder !== null ? (string) $this->evaluate($this->placeholder) : null;
    }

    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        $arr = array_merge(parent::toArray(), [
            'rules' => $this->getRules(),
            'placeholder' => $this->getPlaceholder(),
        ]);
        if ($this->getColumnSpan() !== null) {
            $arr['columnSpan'] = $this->getColumnSpan();
        }
        $gridConfig = $this->getGridLayoutConfig();
        if (! empty($gridConfig['responsive']['span'] ?? [])) {
            $arr['responsive'] = ['span' => $gridConfig['responsive']['span']];
        }
        if (isset($gridConfig['order'])) {
            $arr['order'] = $gridConfig['order'];
        }

        return $arr;
    }

    protected function getType(): string
    {
        $class = static::class;
        $short = class_basename($class);

        return strtolower((string) preg_replace('/Field$/', '', $short));
    }
}
