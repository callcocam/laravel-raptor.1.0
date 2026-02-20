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
 * Base para actions que abrem uma modal no frontend.
 * Configurável: título, descrição, tamanho, botões.
 */
abstract class ModalAction extends CallbackAction
{
    protected Closure|string|null $component = 'modal-action';

    protected Closure|string|null $title = null;

    protected Closure|string|null $description = null;

    protected string $size = 'md';

    protected string $submitText = 'Confirmar';

    protected string $cancelText = 'Cancelar';

    protected string $submitVariant = 'default';

    protected ?string $submitIcon = null;

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

    public function size(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function submitText(string $text): static
    {
        $this->submitText = $text;

        return $this;
    }

    public function cancelText(string $text): static
    {
        $this->cancelText = $text;

        return $this;
    }

    public function submitVariant(string $variant): static
    {
        $this->submitVariant = $variant;

        return $this;
    }

    public function submitIcon(?string $icon): static
    {
        $this->submitIcon = $icon;

        return $this;
    }

    protected function resolveTitle(?Model $model = null): ?string
    {
        if ($this->title instanceof Closure && $model !== null) {
            return ($this->title)($model);
        }

        return is_string($this->title) ? $this->title : null;
    }

    protected function resolveDescription(?Model $model = null): ?string
    {
        if ($this->description instanceof Closure && $model !== null) {
            return ($this->description)($model);
        }

        return is_string($this->description) ? $this->description : null;
    }

    public function render(?Model $model = null, ?Request $request = null): ?array
    {
        $base = parent::render($model, $request);
        if ($base === null) {
            return null;
        }

        return array_merge($base, [
            'title' => $this->resolveTitle($model),
            'description' => $this->resolveDescription($model),
            'size' => $this->size,
            'submitText' => $this->submitText,
            'cancelText' => $this->cancelText,
            'submitVariant' => $this->submitVariant,
            'submitIcon' => $this->submitIcon,
        ]);
    }
}
