<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form\Columns\Types;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithColumns;
use Callcocam\LaravelRaptor\Support\Form\Columns\Column;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RepeaterField extends Column
{
    use WithColumns;

    /** @var Closure|array<int, AbstractColumn> */
    protected Closure|array $columns = [];

    protected Closure|string|null $component = 'form-field-repeater';

    protected bool $reorderable = true;

    protected ?int $minItems = null;

    protected ?int $maxItems = null;

    protected Closure|string|null $addLabel = null;

    protected bool $collapsible = true;

    protected bool $defaultOpen = true;

    public function reorderable(bool $value = true): static
    {
        $this->reorderable = $value;

        return $this;
    }

    public function minItems(?int $value): static
    {
        $this->minItems = $value;

        return $this;
    }

    public function maxItems(?int $value): static
    {
        $this->maxItems = $value;

        return $this;
    }

    public function addLabel(?string $label): static
    {
        $this->addLabel = $label;

        return $this;
    }

    public function getReorderable(): bool
    {
        return $this->reorderable;
    }

    public function getMinItems(): ?int
    {
        return $this->minItems;
    }

    public function getMaxItems(): ?int
    {
        return $this->maxItems;
    }

    public function getAddLabel(): ?string
    {
        return $this->addLabel !== null ? (string) $this->evaluate($this->addLabel) : null;
    }

    public function collapsible(bool $value = true): static
    {
        $this->collapsible = $value;

        return $this;
    }

    public function defaultOpen(bool $value = true): static
    {
        $this->defaultOpen = $value;

        return $this;
    }

    public function getCollapsible(): bool
    {
        return $this->collapsible;
    }

    public function getDefaultOpen(): bool
    {
        return $this->defaultOpen;
    }

    protected function getType(): string
    {
        return 'repeater';
    }

    /**
     * Valor do repeater para o payload do form (array de itens).
     */
    public function getFormValues(?Model $model = null, ?Request $request = null): array
    {
        $value = $this->getValueUsing($request, $model);
        if ($value !== null && is_array($value)) {
            return [$this->getName() => $value];
        }
        if ($model !== null) {
            $raw = $model->getAttribute($this->getName());
            if (is_array($raw)) {
                return [$this->getName() => $raw];
            }
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);

                return [$this->getName() => is_array($decoded) ? $decoded : []];
            }
        }
        if ($this->hasDefaultUsing()) {
            $default = $this->getDefaultUsing($request, $model);

            return [$this->getName() => is_array($default) ? $default : []];
        }

        return [$this->getName() => []];
    }

    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        $children = $this->getColumns($model, $request);
        $children = is_array($children) ? $children : [];

        $arr = array_merge(parent::toArray($model, $request), [
            'type' => 'repeater',
            'reorderable' => $this->reorderable,
            'minItems' => $this->minItems,
            'maxItems' => $this->maxItems,
            'addLabel' => $this->getAddLabel(),
            'collapsible' => $this->collapsible,
            'defaultOpen' => $this->defaultOpen,
            'fields' => array_map(function (AbstractColumn $col) use ($model, $request) {
                return $col->toArray($model, $request);
            }, $children),
        ]);
        if ($this->getGridColumns() !== null) {
            $arr['gridColumns'] = $this->getGridColumns();
        }
        if ($this->getGap() !== null) {
            $arr['gap'] = $this->getGap();
        }

        return $arr;
    }
}
