<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Interacts;

use Callcocam\LaravelRaptor\Support\Actions\AbstractAction;
use Closure;

trait WithFooterActions
{
    protected Closure|array $footerActions = [];

    public function footerActions(Closure|array $actions): static
    {
        $this->footerActions = $actions;

        return $this;
    }

    public function addFooterAction(AbstractAction $action): static
    {
        $this->footerActions[] = $action;

        return $this;
    }

    /**
     * @return AbstractAction[]
     */
    public function getFooterActions(): array
    {
        $resolved = $this->evaluate($this->footerActions);

        return is_array($resolved) ? $resolved : [];
    }

    public function hasFooterActions(): bool
    {
        return count($this->getFooterActions()) > 0;
    }
}
