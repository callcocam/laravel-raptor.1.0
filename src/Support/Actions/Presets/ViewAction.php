<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\UrlAction;

/**
 * Atalho pré-configurado para visualizar registro.
 *
 * Uso: ViewAction::make('view')->route('products.show', fn($m) => ['product' => $m])
 * Já vem com: ícone Eye, variant ghost, inertia true, policy 'view'.
 */
class ViewAction extends UrlAction
{
    protected function setUp(): void
    {
        $this
            ->label('Visualizar')
            ->icon('Eye')
            ->variant('ghost')
            ->policy('view')
            ->inertia();
    }
}
