<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Interacts;

use Callcocam\LaravelRaptor\Support\Actions\AbstractAction;
use Closure;

trait WithHeaderActions
{
    protected Closure|array $headerActions = [];

    public function headerActions(Closure|array $actions): static
    {
        $this->headerActions = $actions;

        return $this;
    }

    public function addHeaderAction(AbstractAction $action): static
    {
        $this->headerActions[] = $action;

        return $this;
    }

    /**
     * @return AbstractAction[]
     */
    public function getHeaderActions(): array
    {
        $resolved = $this->evaluate($this->headerActions);

        return is_array($resolved) ? $resolved : [];
    }

    public function hasHeaderActions(): bool
    {
        return count($this->getHeaderActions()) > 0;
    }
}
