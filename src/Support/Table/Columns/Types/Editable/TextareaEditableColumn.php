<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types\Editable;

use Closure;

class TextareaEditableColumn extends BaseEditableColumn
{
    protected string|Closure|null $component = 'editable-textarea';

    protected ?int $rows = 3;

    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function getRows(): int
    {
        return $this->rows ?? 3;
    }

    public function getInputType(): string
    {
        return 'textarea';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'rows' => $this->getRows(),
        ]);
    }
}
