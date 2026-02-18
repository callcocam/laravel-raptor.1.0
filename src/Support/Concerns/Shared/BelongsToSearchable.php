<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Shared;

use Closure;

trait BelongsToSearchable
{
    protected Closure|bool $searchable = false;

    public function searchable(Closure|bool $value = true): static
    {
        $this->searchable = $value;

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->evaluate($this->searchable);
    }
}
