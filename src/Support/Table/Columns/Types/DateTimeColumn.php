<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types;

use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToDateTime;
use Callcocam\LaravelRaptor\Support\Table\Columns\Column;
use Closure;

class DateTimeColumn extends Column
{
    use BelongsToDateTime;

    protected Closure|string|null $component = 'datetime-table-column';

    public function render(mixed $value, $row = null): mixed
    {
        $formatted = $this->formatDateTime($value);

        return $this->getFormattedValue($formatted ?? $value, $row);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'format' => $this->getFormat(),
            'timezone' => $this->getTimezone(),
            'showDate' => $this->showsDate(),
            'showTime' => $this->showsTime(),
        ]);
    }
}
