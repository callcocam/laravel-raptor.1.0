<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToRelationship;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToSearchable;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToSortable;
use Closure;

class Column extends AbstractColumn
{
    use BelongsToRelationship;
    use BelongsToSearchable;
    use BelongsToSortable;

    protected ?Closure $formatting = null;

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
