<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\ModalFormAction;

/**
 * Atalho pré-configurado para importar dados (header action).
 *
 * Uso: ImportAction::make('import')->columns([...])->onSubmit(fn(...) => ...)
 * Já vem com: ícone Upload, variant outline, modal configurada.
 */
class ImportAction extends ModalFormAction
{
    protected function setUp(): void
    {
        $this
            ->label('Importar')
            ->icon('Upload')
            ->variant('outline')
            ->title('Importar dados')
            ->description('Selecione o arquivo para importação.')
            ->submitText('Importar')
            ->submitIcon('Upload')
            ->tooltip('Importar dados');
    }
}
