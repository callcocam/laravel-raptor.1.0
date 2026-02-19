<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Interacts;

use Callcocam\LaravelRaptor\Support\Actions\AbstractAction;
use Closure;

trait WithActions
{
    protected Closure|array $actions = [];

    public function actions(Closure|array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function addAction(AbstractAction $action): static
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * @return AbstractAction[]
     */
    public function getActions(): array
    {
        $resolved = $this->evaluate($this->actions);

        return is_array($resolved) ? $resolved : [];
    }

    public function hasActions(): bool
    {
        return count($this->getActions()) > 0;
    }
}
