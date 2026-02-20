<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\ConfirmAction;

/**
 * Atalho pré-configurado para excluir registro.
 *
 * Uso: DeleteAction::make('delete')
 * Já vem com: ícone Trash2, variant destructive, confirmação, policy 'delete'.
 */
class DeleteAction extends ConfirmAction
{
    protected function setUp(): void
    {
        $this
            ->label('Excluir')
            ->icon('Trash2')
            ->variant('destructive')
            ->title('Confirmar exclusão')
            ->description('Deseja realmente excluir este registro?')
            ->confirmText('Sim, excluir')
            ->cancelText('Cancelar')
            ->confirmVariant('destructive')
            ->confirmIcon('Trash2')
            ->policy('delete')
            ->visible(fn($model) => !$model->trashed()) // Não mostrar se só estiver vendo itens excluídos
            ->executeUsing(fn($model) => $model->delete());
    }
}
