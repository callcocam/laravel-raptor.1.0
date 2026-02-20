<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\Types\ConfirmAction;

/**
 * Atalho pré-configurado para restaurar registro excluído (SoftDelete).
 * Só aparece quando o filtro `trashed` está ativo (with/only).
 *
 * Uso: RestoreAction::make('restore')
 */
class RestoreAction extends ConfirmAction
{
    /**
     * Indica que o controller deve buscar o model com withTrashed().
     */
    protected bool $requiresWithTrashed = true;

    protected function setUp(): void
    {
        $this
            ->label('Restaurar')
            ->icon('ArchiveRestore')
            ->variant('outline')
            ->title('Confirmar restauração')
            ->description('Deseja restaurar este registro?')
            ->confirmText('Sim, restaurar')
            ->cancelText('Cancelar')
            ->confirmVariant('default')
            ->confirmIcon('ArchiveRestore')
            ->visible(fn ($model) => $model->trashed())
            ->executeUsing(fn ($model) => $model->restore());
    }

    public function requiresWithTrashed(): bool
    {
        return $this->requiresWithTrashed;
    }
}
