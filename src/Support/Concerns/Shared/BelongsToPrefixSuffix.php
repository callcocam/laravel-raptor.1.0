<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Shared;

use Closure;

trait BelongsToPrefixSuffix
{
    protected Closure|string|null $prefix = null;

    protected Closure|string|null $suffix = null;

    /**
     * Define um prefixo para o valor da coluna
     */
    public function prefix(Closure|string|null $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Define um sufixo para o valor da coluna
     */
    public function suffix(Closure|string|null $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Remove o prefixo
     */
    public function withoutPrefix(): static
    {
        $this->prefix = null;

        return $this;
    }

    /**
     * Remove o sufixo
     */
    public function withoutSuffix(): static
    {
        $this->suffix = null;

        return $this;
    }

    /**
     * Obtém o prefixo
     */
    public function getPrefix(): ?string
    {
        if ($this->prefix === null) {
            return null;
        }

        return $this->evaluate($this->prefix);
    }

    /**
     * Obtém o sufixo
     */
    public function getSuffix(): ?string
    {
        if ($this->suffix === null) {
            return null;
        }

        return $this->evaluate($this->suffix);
    }

    /**
     * Verifica se existe prefixo
     */
    public function hasPrefix(): bool
    {
        return $this->prefix !== null;
    }

    /**
     * Verifica se existe sufixo
     */
    public function hasSuffix(): bool
    {
        return $this->suffix !== null;
    }
}
