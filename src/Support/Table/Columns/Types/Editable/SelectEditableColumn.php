<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types\Editable;

use Closure;

class SelectEditableColumn extends BaseEditableColumn
{

    protected string|Closure|null $component = 'editable-select';

    protected ?Closure $optionsCallback = null;

    /**
     * Define opções para select
     */
    public function options(array|Closure $options): static
    {
        if (is_array($options)) {
            $this->optionsCallback = static fn() => $options;
        } else {
            $this->optionsCallback = $options;
        }

        return $this;
    }

    public function getInputType(): string
    {
        return 'select';
    }

    public function render(mixed $value, $row = null): mixed
    {
        $base =  parent::render($value, $row);

        return array_merge($base, [
            'options' => $this->getOptions(),
        ]);
    }
}
