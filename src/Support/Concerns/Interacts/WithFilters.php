<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Interacts;

use Closure;

trait WithFilters
{
    protected Closure|array $filters = [];

    public function filters(Closure|array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    public function getFilters(): array
    {
        return $this->evaluate($this->filters);
    }
}
