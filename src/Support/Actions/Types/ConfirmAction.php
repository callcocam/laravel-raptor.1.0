<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Types;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Action com confirmação antes de executar.
 * O frontend exibe um Dialog de confirmação com título, descrição e botões customizáveis.
 */
class ConfirmAction extends CallbackAction
{
    protected Closure|string|null $title = null;

    protected Closure|string|null $description = null;

    protected string $confirmText = 'Confirmar';

    protected string $cancelText = 'Cancelar';

    protected string $confirmVariant = 'default';

    protected ?string $confirmIcon = null;

    public function title(Closure|string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function description(Closure|string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function confirmText(string $text): static
    {
        $this->confirmText = $text;

        return $this;
    }

    public function cancelText(string $text): static
    {
        $this->cancelText = $text;

        return $this;
    }

    public function confirmVariant(string $variant): static
    {
        $this->confirmVariant = $variant;

        return $this;
    }

    public function confirmIcon(?string $icon): static
    {
        $this->confirmIcon = $icon;

        return $this;
    }

    public function render(?Model $model = null, ?Request $request = null): ?array
    {
        $base = parent::render($model, $request);
        if ($base === null) {
            return null;
        }

        $title = ($this->title instanceof Closure && $model !== null)
            ? ($this->title)($model)
            : (is_string($this->title) ? $this->title : null);

        $description = ($this->description instanceof Closure && $model !== null)
            ? ($this->description)($model)
            : (is_string($this->description) ? $this->description : null);

        return array_merge($base, [
            'title' => $title,
            'description' => $description,
            'confirmText' => $this->confirmText,
            'cancelText' => $this->cancelText,
            'confirmVariant' => $this->confirmVariant,
            'confirmIcon' => $this->confirmIcon,
        ]);
    }
}
