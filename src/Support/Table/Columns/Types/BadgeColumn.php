<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types;

use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToColor;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToIcon;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToPrefixSuffix;
use Callcocam\LaravelRaptor\Support\Table\Columns\Column;
use Closure;

class BadgeColumn extends Column
{
    use BelongsToColor;
    use BelongsToIcon;
    use BelongsToPrefixSuffix;

    protected Closure|string|null $component = 'badge-table-column';

    protected Closure|string|null $colorMap = null;

    protected bool $isDot = false;

    /**
     * Define um mapa de cores baseado no valor
     * Exemplo: ['active' => 'green', 'inactive' => 'red', 'pending' => 'yellow']
     */
    public function colorMap(Closure|array $map): static
    {
        if (is_array($map)) {
            $this->colorMap = static fn() => $map;
        } else {
            $this->colorMap = $map;
        }

        return $this;
    }

    /**
     * Torna visível apenas um ponto (dot) ao invés do texto
     */
    public function dot(bool $isDot = true): static
    {
        $this->isDot = $isDot;

        return $this;
    }

    /**
     * Obtém o mapa de cores
     */
    public function getColorMap(): array
    {
        if ($this->colorMap === null) {
            return [];
        }

        return (array) $this->evaluate($this->colorMap);
    }

    /**
     * Obtém a cor baseada no valor
     */
    public function getColorForValue(mixed $value): ?string
    {
        $map = $this->getColorMap();

        if (empty($map)) {
            return $this->getColor();
        }

        $key = (string) $value;

        return $map[$key] ?? $this->getColor();
    }

    /**
     * Verifica se é um ponto (dot)
     */
    public function isDot(): bool
    {
        return $this->isDot;
    }

    public function render(mixed $value, $row = null): mixed
    {
        return $this->getFormattedValue($value, $row);
    }


    public function getInputType(): string
    {
        return 'number';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'color' => $this->getColor(),
            'colorMap' => $this->getColorMap(),
            'icon' => $this->hasIcon() ? $this->getIcon() : null,
            'prefix' => $this->getPrefix(),
            'suffix' => $this->getSuffix(),
            'isDot' => $this->isDot(),
            'inputType' => $this->getInputType()
        ]);
    }
}
