<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Filters;

use Callcocam\LaravelRaptor\Support\Table\FilterBuilder;
use Closure;

class SearchFilter extends FilterBuilder
{
    protected Closure|string|null $component = 'search-table-filter';

    protected function setUp(): void
    {
        $this->queryUsing(function ($query, $value) {
            $query->where($this->getName(), 'like', "%{$value}%");
        });
    }
}
