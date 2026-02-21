<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form\Columns;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Concerns\HasGridLayout;
use Closure;

abstract class Column extends AbstractColumn
{
    use HasGridLayout;
    /** @var array<int, string>|string|Closure|null */
    protected array|string|Closure|null $rules = null;

    protected Closure|string|null $placeholder = null;

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

    public function toArray(): array
    {
        $arr = array_merge(parent::toArray(), [
            'rules' => $this->getRules(),
            'placeholder' => $this->getPlaceholder(),
        ]);
        if ($this->getColumnSpan() !== null) {
            $arr['columnSpan'] = $this->getColumnSpan();
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
