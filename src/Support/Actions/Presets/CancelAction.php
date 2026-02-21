<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\UrlAction;

/**
 * Action de cancelar (footer do form): volta para listagem.
 * Uso: CancelAction::make('cancel')->route('products.index')
 */
class CancelAction extends UrlAction
{
    protected function setUp(): void
    {
        $this
            ->label('Cancelar')
            ->icon('X')
            ->variant('outline')
            ->inertia();
    }
}
