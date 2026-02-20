<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\ConfirmAction;

/**
 * Atalho pré-configurado para excluir permanentemente um registro (SoftDelete).
 * Só aparece quando o filtro `trashed` está ativo (with/only).
 *
 * Uso: ForceDeleteAction::make('force-delete')
 */
class ForceDeleteAction extends ConfirmAction
{
    /**
     * Indica que o controller deve buscar o model com withTrashed().
     */
    protected bool $requiresWithTrashed = true;

    protected function setUp(): void
    {
        $this
            ->label('Excluir permanentemente')
            ->icon('Trash2')
            ->variant('destructive')
            ->title('Excluir permanentemente')
            ->description('Esta ação é irreversível. O registro será excluído definitivamente.')
            ->confirmText('Sim, excluir')
            ->cancelText('Cancelar')
            ->confirmVariant('destructive')
            ->confirmIcon('Trash2')
            ->visible(fn ($model) => $model->trashed())
            ->executeUsing(fn ($model) => $model->forceDelete());
    }

    public function requiresWithTrashed(): bool
    {
        return $this->requiresWithTrashed;
    }
}
