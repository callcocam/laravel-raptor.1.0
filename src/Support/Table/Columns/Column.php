<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Concerns;
use Closure;

class Column extends AbstractColumn
{
    use  Concerns\Shared\BelongsToRelationship;
    use  Concerns\Shared\BelongsToSearchable;
    use  Concerns\Shared\BelongsToSortable;
    use Concerns\Shared\BelongsToOptions;

    protected ?Closure $formatting = null;

    /**
     * The component to use for the column.
     */
    protected Closure|string|null $component = 'text-table-column';

    public function formatting(Closure $formatting): static
    {
        $this->formatting = $formatting;

        return $this;
    }

    public function getFormattedValue(mixed $value, mixed $row = null): mixed
    {
        if ($this->formatting === null) {
            return $value;
        }

        return $this->evaluate($this->formatting, [
            'value' => $value,
            'row' => $row,
        ]);
    }

    public function toArray(): array
    {
        $arr = array_merge(parent::toArray(), [
            'sortable' => $this->isSortable(),
            'searchable' => $this->isSearchable(),
        ]);

        if ($this->isRelationship()) {
            $arr['relationship'] = $this->getRelationshipPath();
        }

        return $arr;
    }
}
