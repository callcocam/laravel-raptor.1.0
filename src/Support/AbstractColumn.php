<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support;

use Closure;
use Illuminate\Support\Str;

abstract class AbstractColumn
{
    use Concerns\EvaluatesClosures;
    use Concerns\FactoryPattern;
    use Concerns\Shared\BelongsToId;
    use Concerns\Shared\BelongsToLabel;
    use Concerns\Shared\BelongsToName;

    protected Closure|array $column = [];

    protected Closure|string|null $component = null;

    public function __construct(string $name, ?string $label = null)
    {
        $this->name = $name;
        $this->label = $label ?? Str::title($name);
    }

    protected function setUp()
    {

        //
    }

    public function component(Closure|string $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function getComponent(): ?string
    {
        return $this->evaluate($this->component);
    }

    /**
     * Serialização para o payload do frontend (Vue, React, Livewire, Blade).
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'component' => $this->getComponent(),
        ];
    }

    /**
     * Tipo da coluna para o frontend (ex: text, badge). Subclasses sobrescrevem.
     */
    protected function getType(): string
    {
        $class = static::class;
        $short = class_basename($class);

        return Str::of($short)->replace('Column', '')->lower()->toString();
    }

    /**
     * Renderiza o valor da célula (backend). Subclasses podem sobrescrever.
     */
    public function render(mixed $value, mixed $row = null): mixed
    {
        return $this->getFormattedValue($value, $row);
    }

    /**
     * Valor formatado para exibição. Subclasses podem sobrescrever (ex: TextColumn).
     */
    public function getFormattedValue(mixed $value, mixed $row = null): mixed
    {
        return $value;
    }
}
