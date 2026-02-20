<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\ConfirmAction;

/**
 * Atalho pré-configurado para excluir múltiplos registros (soft delete).
 * Só aparece quando não estiver filtrando por itens excluídos.
 *
 * Uso: BulkDeleteAction::make('bulk-delete')
 */
class BulkDeleteAction extends ConfirmAction
{
    protected function setUp(): void
    {
        $this
            ->label('Excluir selecionados')
            ->icon('Trash2')
            ->variant('destructive')
            ->title('Excluir registros')
            ->description('Deseja excluir os registros selecionados?')
            ->confirmText('Sim, excluir')
            ->confirmVariant('destructive')
            ->visible(fn () => ! in_array(request()->input('trashed'), ['with', 'only']))
            ->executeUsing(function ($model, $request) {
                $ids = $request->input('ids', []);
                $model->newQuery()
                    ->whereIn($model->getKeyName(), $ids)
                    ->delete();
            });
    }
}
