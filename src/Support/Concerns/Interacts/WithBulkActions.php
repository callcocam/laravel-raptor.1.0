<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Interacts;

use Callcocam\LaravelRaptor\Support\Actions\AbstractAction;
use Closure;

trait WithBulkActions
{
    protected Closure|array $bulkActions = [];

    public function bulkActions(Closure|array $actions): static
    {
        $this->bulkActions = $actions;

        return $this;
    }

    public function addBulkAction(AbstractAction $action): static
    {
        $this->bulkActions[] = $action;

        return $this;
    }

    /**
     * @return AbstractAction[]
     */
    public function getBulkActions(): array
    {
        $resolved = $this->evaluate($this->bulkActions);

        return is_array($resolved) ? $resolved : [];
    }

    public function hasBulkActions(): bool
    {
        return count($this->getBulkActions()) > 0;
    }
}
