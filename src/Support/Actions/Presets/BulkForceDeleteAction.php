<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\ConfirmAction;

/**
 * Atalho pré-configurado para excluir permanentemente múltiplos registros (SoftDelete).
 * Só aparece quando estiver filtrando por itens excluídos.
 *
 * Uso: BulkForceDeleteAction::make('bulk-force-delete')
 */
class BulkForceDeleteAction extends ConfirmAction
{
    protected bool $requiresWithTrashed = true;

    protected function setUp(): void
    {
        $this
            ->label('Excluir permanentemente')
            ->icon('Trash2')
            ->variant('destructive')
            ->title('Excluir permanentemente')
            ->description('Esta ação é irreversível. Os registros serão excluídos definitivamente.')
            ->confirmText('Sim, excluir')
            ->confirmVariant('destructive')
            ->confirmIcon('Trash2')
            ->visible(fn ($model) => in_array(request()->input('trashed'), ['with', 'only']))
            ->executeUsing(function ($model, $request) {
                $ids = $request->input('ids', []);
                $model->newQuery()

                    ->withTrashed()
                    ->whereIn($model->getKeyName(), $ids)
                    ->forceDelete();
            });
    }

    public function requiresWithTrashed(): bool
    {
        return $this->requiresWithTrashed;
    }
}
