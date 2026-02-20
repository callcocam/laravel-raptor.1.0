<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Filters;

use Callcocam\LaravelRaptor\Support\Table\FilterBuilder;
use Closure;

/**
 * TrashedFilter - Filter para registros com SoftDelete.
 *
 * Valores disponíveis:
 * - '' ou null  → padrão (sem deletados)
 * - 'with'      → withTrashed() (todos incluindo deletados)
 * - 'only'      → onlyTrashed() (apenas deletados)
 */
class TrashedFilter extends FilterBuilder
{
    protected Closure|string|null $component = 'filter-trashed';

    public function __construct(string $name = 'trashed', ?string $label = null)
    {
        parent::__construct($name, $label ?? 'Excluídos');
    }

    protected function setUp(): void
    {
        $this->queryUsing(function ($query, $value) {
            return match ($value) {
                'with' => $query->withTrashed(),
                'only' => $query->onlyTrashed(),
                default => $query,
            };
        });
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => [
                ['value' => 'with', 'label' => 'Com excluídos'],
                ['value' => 'only', 'label' => 'Apenas excluídos'],
            ],
        ]);
    }
}
