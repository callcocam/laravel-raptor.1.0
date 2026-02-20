<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\ConfirmAction;

/**
 * Atalho pré-configurado para restaurar múltiplos registros excluídos (SoftDelete).
 * Só aparece quando estiver filtrando por itens excluídos.
 *
 * Uso: BulkRestoreAction::make('bulk-restore')
 */
class BulkRestoreAction extends ConfirmAction
{
    protected bool $requiresWithTrashed = true;

    protected function setUp(): void
    { 
        $this
            ->label('Restaurar selecionados')
            ->icon('ArchiveRestore')
            ->variant('outline')
            ->title('Restaurar registros')
            ->description('Deseja restaurar os registros selecionados?')
            ->confirmText('Sim, restaurar')
            ->confirmVariant('default')
            ->confirmIcon('ArchiveRestore')
            ->visible(fn ($model) => in_array(request()->input('trashed'), ['with', 'only']))
            ->executeUsing(function ($model, $request) {
                $ids = $request->input('ids', []); 
                $model->newQuery()
                    ->withTrashed()
                    ->whereIn($model->getKeyName(), $ids)
                    ->restore();
            });
    }

    public function requiresWithTrashed(): bool
    {
        return $this->requiresWithTrashed;
    }
}
