<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types\Editable;

use Closure;

/**
 * Column genérica com tipo dinâmico
 * Use as subclasses mais específicas quando possível:
 * - TextEditableColumn
 * - NumberEditableColumn
 * - EmailEditableColumn
 * - TextareaEditableColumn
 * - SelectEditableColumn
 */
class EditableColumn extends BaseEditableColumn
{
    protected Closure|string|null $inputType = 'text';

    protected ?Closure $optionsCallback = null;

    /**
     * Define o tipo de input (text, select, textarea, email, number, etc)
     */
    public function inputType(Closure|string $type): static
    {
        $this->inputType = $type;

        return $this;
    }

    /**
     * Define opções para selects
     */
    public function options(array|Closure $options): static
    {
        if (is_array($options)) {
            $this->optionsCallback = static fn () => $options;
        } else {
            $this->optionsCallback = $options;
        }

        return $this;
    }

    public function getInputType(): string
    {
        return (string) $this->evaluate($this->inputType);
    }
}
