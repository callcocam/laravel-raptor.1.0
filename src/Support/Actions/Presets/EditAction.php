<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\UrlAction;

/**
 * Atalho pré-configurado para editar registro.
 *
 * Uso: EditAction::make('edit')->route('products.edit', fn($m) => ['product' => $m])
 * Já vem com: ícone Edit, variant outline, inertia true, policy 'update'.
 */
class EditAction extends UrlAction
{
    protected function setUp(): void
    {
        $this
            ->label('Editar')
            ->icon('Edit')
            ->variant('outline')
            ->policy('update')
            ->inertia();
    }
}
