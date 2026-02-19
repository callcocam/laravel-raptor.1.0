<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\UrlAction;

/**
 * Atalho pré-configurado para voltar (header action).
 *
 * Uso: BackAction::make('back')->route('products.index')
 * Já vem com: ícone ArrowLeft, variant outline, inertia true.
 */
class BackAction extends UrlAction
{
    protected function setUp(): void
    {
        $this
            ->label('Voltar')
            ->icon('ArrowLeft')
            ->variant('outline')
            ->inertia();
    }
}
