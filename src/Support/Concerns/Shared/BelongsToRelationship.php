<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Shared;

use Illuminate\Support\Str;

/**
 * Detecta colunas com dot notation (ex.: user.name, category.parent.title).
 * Sabe separar relationship path do campo final.
 */
trait BelongsToRelationship
{
    /**
     * Relationship explícito (sobrescreve auto-detecção).
     */
    protected ?string $relationship = null;

    /**
     * Coluna no relacionamento (sobrescreve auto-detecção).
     */
    protected ?string $relationshipColumn = null;

    public function relationship(?string $relationship, ?string $column = null): static
    {
        $this->relationship = $relationship;
        $this->relationshipColumn = $column;

        return $this;
    }

    /**
     * Tem relationship? (explícito ou dot notation no name).
     */
    public function isRelationship(): bool
    {
        if ($this->relationship !== null) {
            return true;
        }

        return Str::contains($this->getName(), '.');
    }

    /**
     * Path do relationship (ex: "user", "category.parent").
     */
    public function getRelationshipPath(): ?string
    {
        if ($this->relationship !== null) {
            return $this->relationship;
        }

        $name = $this->getName();
        if (! Str::contains($name, '.')) {
            return null;
        }

        $parts = explode('.', $name);
        array_pop($parts);

        return implode('.', $parts);
    }

    /**
     * Campo final no relacionamento (ex: "name", "title").
     */
    public function getRelationshipColumn(): ?string
    {
        if ($this->relationshipColumn !== null) {
            return $this->relationshipColumn;
        }

        $name = $this->getName();
        if (! Str::contains($name, '.')) {
            return null;
        }

        $parts = explode('.', $name);

        return array_pop($parts);
    }

    /**
     * Coluna qualificada para queries (tabela do relationship).
     * Para sort/search em relationships usa whereHas + column.
     */
    public function getQualifiedColumn(): string
    {
        if ($this->isRelationship()) {
            return $this->getRelationshipColumn() ?? $this->getName();
        }

        return $this->getName();
    }
}
