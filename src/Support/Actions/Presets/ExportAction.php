<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\CallbackAction;

/**
 * Atalho pré-configurado para exportar dados (header action).
 *
 * Uso: ExportAction::make('export')->executeUsing(fn($model, $request) => ...)
 * Já vem com: ícone Download, variant outline.
 */
class ExportAction extends CallbackAction
{
    protected function setUp(): void
    {
        $this
            ->label('Exportar')
            ->icon('Download')
            ->variant('outline')
            ->tooltip('Exportar dados');
    }
}
