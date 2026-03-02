<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Shared;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Actions\AbstractAction;
use Closure;

trait BelongsToHelpers
{
    protected mixed $default = '';

    protected string|Closure|null $helpText = null;

    protected string|array|Closure|AbstractColumn|AbstractAction|null $hint = null;

    /** Prepend/prefix: conteúdo ou action antes do campo (prepend é atalho de prefix). */
    protected string|array|Closure|AbstractAction|null $prefix = null;

    /** Append/suffix: conteúdo ou action depois do campo (append é atalho de suffix). */
    protected string|array|Closure|AbstractAction|null $suffix = null;

    protected ?string $placeholder = null;

    protected bool $isReadOnly = false;

    protected bool $isDisabled = false;

    /**
     * Define o valor padrão do campo
     */
    public function default(mixed $value): static
    {
        $this->default = $value;

        return $this;
    }

    /**
     * Define o campo como somente leitura (readonly)
     */
    public function readonly(bool $condition = true): static
    {
        $this->isReadOnly = $condition;

        return $this;
    }

    /**
     * Define o campo como desabilitado (disabled)
     */
    public function disabled(bool $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    /**
     * Define o texto de ajuda (exibido abaixo do campo)
     */
    public function helpText(string|Closure $text): static
    {
        $this->helpText = $text;

        return $this;
    }

    /**
     * Define o texto de ajuda (exibido abaixo do campo)
     */
    public function helperText(string|Closure $text): static
    {
        $this->helpText = $text;

        return $this;
    }

    /**
     * Define uma dica ou actions (exibidas abaixo do campo)
     * Pode ser:
     * - String simples: texto de ajuda
     * - Array: lista de actions a serem renderizadas
     * - Closure: função que retorna string ou array de actions
     * - AbstractColumn ou AbstractAction: convertido para payload no getHint()
     *
     * @param  string|array|Closure|AbstractColumn|AbstractAction  $hint  Texto ou array de actions
     */
    public function hint(string|array|Closure|AbstractColumn|AbstractAction $hint): static
    {
        $this->hint[] = $hint;

        return $this;
    }

    /**     * Define actions como hint (alias mais semântico para hint com array)
     *
     * @param  array|Closure  $actions  Array de actions ou Closure que retorna array
     */
    public function hintActions(array|Closure $actions): static
    {
        $this->hint = $actions;

        return $this;
    }

    /**
     * Conteúdo/ação antes do campo (atalho de prefix).
     */
    public function prepend(string|array|Closure|AbstractAction $content): static
    {
        return $this->prefix($content);
    }

    /**
     * Conteúdo/ação depois do campo (atalho de suffix).
     */
    public function append(string|array|Closure|AbstractAction $content): static
    {
        return $this->suffix($content);
    }

    /**
     * Prefixo ao campo (ex: R$, +55) ou action. Prepend é atalho.
     */
    public function prefix(string|array|Closure|AbstractAction $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Sufixo ao campo (ex: kg, m², %) ou action. Append é atalho.
     */
    public function suffix(string|array|Closure|AbstractAction $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Define o placeholder do campo
     */
    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Retorna o valor padrão
     */
    public function getDefault(): mixed
    {
        return $this->evaluate($this->default);
    }

    /**
     * Retorna o texto de ajuda
     */
    public function getHelpText(): ?string
    {
        return $this->evaluate($this->helpText);
    }

    /**
     * Retorna a dica (pode ser string ou array de actions)
     */
    public function getHint(): string|array|null
    {
        $hints = [];
        if (is_array($this->hint)) {
            foreach ($this->hint as $hint) {
                if ($hint instanceof AbstractColumn) {
                    $hints[] = $hint->toArray($this->getModel());
                } elseif ($hint instanceof AbstractAction) {
                    $hints[] = $hint->toArray(null, null);
                } else {
                    $hints[] = $this->evaluate($hint);
                }
            }

            return $hints;
        }

        $evaluated = $this->evaluate($this->hint);
        if ($evaluated instanceof AbstractAction) {
            return [$evaluated->toArray(null, null)];
        }
        if ($evaluated instanceof AbstractColumn) {
            return [$evaluated->toArray($this->getModel())];
        }

        return $evaluated;
    }

    /**
     * Retorna o conteúdo prepend (mesmo que getPrefix).
     */
    public function getPrepend(): string|array|null
    {
        return $this->getPrefix();
    }

    /**
     * Retorna o conteúdo append (mesmo que getSuffix).
     */
    public function getAppend(): string|array|null
    {
        return $this->getSuffix();
    }

    /**
     * Normaliza prefix/suffix para string ou array de payloads (para action).
     *
     * @return string|array<int, array<string, mixed>>|null
     */
    private function normalizeAddonValue(mixed $value): string|array|null
    {
        $evaluated = $this->evaluate($value);
        if ($evaluated === null) {
            return null;
        }
        if ($evaluated instanceof AbstractAction) {
            return [$evaluated->toArray(null, null)];
        }
        if (is_array($evaluated)) {
            $out = [];
            foreach ($evaluated as $item) {
                if ($item instanceof AbstractAction) {
                    $out[] = $item->toArray(null, null);
                } elseif (is_string($item) || is_array($item)) {
                    $out[] = $item;
                }
            }

            return $out;
        }

        return is_string($evaluated) ? $evaluated : null;
    }

    /**
     * Retorna o prefixo (string ou array de actions).
     */
    public function getPrefix(): string|array|null
    {
        return $this->normalizeAddonValue($this->prefix);
    }

    /**
     * Retorna o sufixo (string ou array de actions).
     */
    public function getSuffix(): string|array|null
    {
        return $this->normalizeAddonValue($this->suffix);
    }

    /**
     * Retorna o placeholder
     */
    public function getPlaceholder(): ?string
    {
        return $this->evaluate($this->placeholder);
    }

    /**
     * Verifica se o campo é somente leitura
     */
    public function isReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    /**
     * Retorna se o campo é readonly (alias para isReadOnly)
     */
    public function getReadOnly(): bool
    {
        return $this->isReadOnly();
    }

    /**
     * Verifica se o campo está desabilitado
     */
    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }

    /**
     * Retorna se o campo está disabled (alias para isDisabled)
     */
    public function getDisabled(): bool
    {
        return $this->isDisabled();
    }
}
