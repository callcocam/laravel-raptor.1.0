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
use Illuminate\Support\Arr;

/**
 * Agrupa campos em uma seção.
 *
 * - relationship('address'): campos são agrupados no objeto da relação (ex.: address.street).
 * - statePath('settings'): campos são agrupados no path do model (ex.: atributo JSON settings).
 * - Nenhum dos dois: apenas separação visual; os campos continuam no mesmo nível (não aninhados).
 */
class SectionField extends Column
{
    use WithColumns;

    protected Closure|string|null $component = 'form-field-section';

    protected ?string $relationship = null;

    protected ?string $statePath = null;

    protected bool $collapsible = false;

    protected bool $defaultOpen = true;

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

    public function relationship(string $name): static
    {
        $this->relationship = $name;

        return $this;
    }

    public function statePath(string $path): static
    {
        $this->statePath = $path;

        return $this;
    }


    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    public function getStatePath(): ?string
    {
        return $this->statePath;
    }



    public function isSection(): bool
    {
        return true;
    }

    /**
     * Valores da seção para o payload. Cada field (e a section) trata seus próprios dados.
     */
    public function getFormValues(?Model $model = null, ?Request $request = null): array
    {
        $children = $this->getColumns($model, $request);

        if ($this->relationship !== null) {
            $related = $model?->getRelationValue($this->relationship);
            $out = [];
            foreach ($children as $col) {
                $out[$col->getName()] = $related?->getAttribute($col->getName());
            }

            return [$this->relationship => $out];
        }

        if ($this->statePath !== null) {
            $nested = $model !== null ? data_get($model, $this->statePath) : null;
            $out = is_array($nested) ? $nested : [];
            foreach ($children as $col) {
                $name = $col->getName();
                if (! Arr::exists($out, $name)) {
                    $out[$name] = $model !== null ? data_get($model, "{$this->statePath}.{$name}") : null;
                }
            }

            return [$this->statePath => $out];
        }

        $merged = [];
        foreach ($children as $col) {
            if (method_exists($col, 'getFormValues')) {
                $merged = array_merge($merged, $col->getFormValues($model, $request));
            }
        }

        return $merged;
    }

    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        $children = $this->getColumns($model, $request);

        return array_merge(parent::toArray($model, $request), [
            'type' => 'section',
            'relationship' => $this->relationship,
            'statePath' => $this->statePath,
            'collapsible' => $this->collapsible,
            'defaultOpen' => $this->defaultOpen,
            'fields' => array_map(function (AbstractColumn $col) use ($model, $request) {
                return $col->toArray($model, $request);
            }, $children),
        ]);
    }
}
