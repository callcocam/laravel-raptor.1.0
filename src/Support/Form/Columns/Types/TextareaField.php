<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form\Columns\Types;

use Callcocam\LaravelRaptor\Support\Form\Columns\Column;
use Closure;

class TextareaField extends Column
{
    protected Closure|string|null $component = 'form-field-textarea';

    protected int $rows = 4;

    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'rows' => $this->getRows(),
        ]);
    }
}
