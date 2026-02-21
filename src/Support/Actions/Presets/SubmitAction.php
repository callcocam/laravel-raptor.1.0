<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Presets;

use Callcocam\LaravelRaptor\Support\Actions\AbstractAction;
use Closure;

/**
 * Action de submit do formulário (footer).
 * Frontend interpreta type 'submit' como botão que submete o form.
 */
class SubmitAction extends AbstractAction
{
    protected Closure|string|null $component = 'button-action';

    protected function setUp(): void
    {
        $this
            ->label('Salvar')
            ->icon('Save')
            ->variant('default');
    }
}
