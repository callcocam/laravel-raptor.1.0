<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Filters;

use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToOptions;
use Callcocam\LaravelRaptor\Support\Table\FilterBuilder;
use Closure;

class SelectFilter extends FilterBuilder
{
    use BelongsToOptions;

    protected Closure|string|null $component = 'filter-select';

    protected function setUp(): void
    {
        $this->queryUsing(function ($query, $value) {
            if (is_string($value) && str_contains($value, ',')) {
                $query->whereIn($this->getName(), explode(',', $value));
            } else {
                $query->where($this->getName(), $value);
            }
            return $query;
        });
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->getOptions(),
        ]);
    }
}
