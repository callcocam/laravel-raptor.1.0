<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Interacts;

use Callcocam\LaravelRaptor\Support\Table\Summarizers\Summarizer;
use Closure;

trait WithSummarizers
{
    /**
     * @var Closure|array<int, Summarizer>
     */
    protected Closure|array $summarizers = [];

    public function summarize(Closure|array $summarizers): static
    {
        $this->summarizers = $summarizers;

        return $this;
    }

    public function addSummarizer(Summarizer $summarizer): static
    {
        $this->summarizers[] = $summarizer;

        return $this;
    }

    /**
     * @return array<int, Summarizer>
     */
    public function getSummarizers(): array
    {
        $evaluated = $this->evaluate($this->summarizers);

        return is_array($evaluated) ? $evaluated : [];
    }

    public function hasSummarizers(): bool
    {
        return count($this->getSummarizers()) > 0;
    }
}
