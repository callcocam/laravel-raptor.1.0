<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Shared;

use Closure;

trait BelongsToSortable
{
    protected Closure|bool $sortable = false;

    /**
     * Coluna do banco para ordenação (quando diferente do name da coluna).
     */
    protected Closure|string|null $sortColumn = null;

    public function sortable(Closure|bool $value = true): static
    {
        $this->sortable = $value;

        return $this;
    }

    public function sortColumn(Closure|string|null $column): static
    {
        $this->sortColumn = $column;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->evaluate($this->sortable);
    }

    public function getSortColumn(): ?string
    {
        return $this->evaluate($this->sortColumn) ?? $this->getName();
    }
}
