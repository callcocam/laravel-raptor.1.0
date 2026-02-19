<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\UrlAction;

/**
 * Atalho pré-configurado para criar novo registro (header action).
 *
 * Uso: CreateAction::make('create')->route('products.create')
 * Já vem com: ícone Plus, variant default, inertia true, policy 'create'.
 */
class CreateAction extends UrlAction
{
    protected function setUp(): void
    {
        $this
            ->label('Novo')
            ->icon('Plus')
            ->variant('default')
            ->policy('create')
            ->inertia();
    }
}
