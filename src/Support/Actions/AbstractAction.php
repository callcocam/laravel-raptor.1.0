<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions;

use Callcocam\LaravelRaptor\Support\Concerns\EvaluatesClosures;
use Callcocam\LaravelRaptor\Support\Concerns\FactoryPattern;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToIcon;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToId;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToLabel;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToName;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToTooltip;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToVisible;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class AbstractAction
{
    use BelongsToIcon;
    use BelongsToId;
    use BelongsToLabel;
    use BelongsToName;
    use BelongsToTooltip;
    use BelongsToVisible;
    use EvaluatesClosures;
    use FactoryPattern;

    protected string $variant = 'default';

    protected Closure|string|null $color = null;

    protected Closure|bool|null $disabled = null;

    public function __construct(string $name, ?string $label = null)
    {
        $this->name = $name;
        $this->label = $label ?? Str::title(str_replace(['-', '_'], ' ', $name));
        $this->id = $name;
        $this->icon = null;
        $this->setUp();
    }

    protected function setUp(): void {}

    public function variant(string $variant): static
    {
        $this->variant = $variant;

        return $this;
    }

    public function color(Closure|string|null $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function disabled(Closure|bool|null $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function getVariant(): string
    {
        return $this->variant;
    }

    public function getColor(): ?string
    {
        if ($this->color instanceof Closure) {
            return ($this->color)();
        }

        return $this->color;
    }

    public function isDisabled(?Model $model = null): bool
    {
        if ($this->disabled === null || $model === null) {
            return false;
        }

        return (bool) $this->evaluate($this->disabled, [
            'model' => $model,
            'item' => $model,
            'record' => $model,
        ]);
    }

    /**
     * Tipo da action para o frontend (ex: url, callback, confirm, modal-form).
     */
    protected function getActionType(): string
    {
        $short = class_basename(static::class);

        return Str::of($short)->replace('Action', '')->kebab()->lower()->toString();
    }

    /**
     * Metadata da action.
     * Model e Request são opcionais (header/bulk actions não têm model por registro).
     */
    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getActionType(),
            'icon' => $this->toStringIcon(),
            'variant' => $this->getVariant(),
            'color' => $this->getColor(),
            'tooltip' => $this->hasTooltip() ? $this->getTooltip() : null,
        ];
    }

    /**
     * Payload serializado para um registro específico ou ação estática (header/bulk).
     * Retorna null se a action não é visível.
     *
     * Usa BelongsToVisible::isVisible() que verifica:
     * 1. Callback customizado (visibleWhen)
     * 2. Laravel Policy (policy)
     * 3. Visibilidade geral (visible)
     */
    public function render(?Model $model = null, ?Request $request = null): ?array
    {
        if (! $this->isVisible($model)) {
            return null;
        }

        return array_merge($this->toArray($model, $request), [
            'disabled' => $this->isDisabled($model),
        ]);
    }
}
