<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Table\Strategies\FilterStrategy;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterBuilder extends AbstractColumn
{
    protected string $component = 'filter-text';

    protected ?Closure $applyCallback = null;

    protected mixed $value = null;

    protected ?FilterStrategy $strategy = null;

    public function __construct(string $name, ?string $label = null)
    {
        parent::__construct($name, $label);
        $this->id($name);
        $this->setUp();
    }

    public function queryUsing(Closure $callback): static
    {
        $this->applyCallback = $callback;

        return $this;
    }

    public function strategy(FilterStrategy $strategy): static
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * Aplica o filtro na query (callback customizado ou FilterStrategy).
     */
    public function applyToQuery(Builder $query, mixed $value): Builder
    {
        if ($value === null || $value === '') {
            return $query;
        }

        if ($this->applyCallback !== null) {
            return ($this->applyCallback)($query, $value);
        }

        if ($this->strategy !== null) {
            return $this->strategy->apply($query, $this->getName(), $value);
        }

        return $query;
    }
}
