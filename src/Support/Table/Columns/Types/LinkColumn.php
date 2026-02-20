<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types;

use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToIcon;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToPrefixSuffix;
use Callcocam\LaravelRaptor\Support\Table\Columns\Column;
use Closure;

class LinkColumn extends Column
{
    use BelongsToIcon;
    use BelongsToPrefixSuffix;

    protected Closure|string|null $component = 'link-table-column';

    protected Closure|string|null $url = null;

    protected bool $openInNewTab = false;

    /**
     * Define a URL do link (pode ser closure para valores dinâmicos)
     */
    public function url(Closure|string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Faz o link abrir em nova aba
     */
    public function openInNewTab(bool $newTab = true): static
    {
        $this->openInNewTab = $newTab;

        return $this;
    }

    /**
     * Obtém a URL do link
     */
    public function getUrl(mixed $value = null, mixed $row = null): ?string
    {
        if ($this->url === null) {
            return null;
        }

        $result = $this->evaluate($this->url, [
            'value' => $value,
            'row' => $row,
        ]);

        return is_string($result) ? $result : null;
    }

    /**
     * Verifica se abre em nova aba
     */
    public function shouldOpenInNewTab(): bool
    {
        return $this->openInNewTab;
    }

    public function render(mixed $value, $row = null): mixed
    {
        return $this->getFormattedValue($value, $row);
    }



    public function getInputType(): string
    {
        return 'link';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'icon' => $this->hasIcon() ? $this->getIcon() : null,
            'prefix' => $this->getPrefix(),
            'suffix' => $this->getSuffix(),
            'openInNewTab' => $this->shouldOpenInNewTab(),
            'type' => $this->getInputType()
        ]);
    }
}
