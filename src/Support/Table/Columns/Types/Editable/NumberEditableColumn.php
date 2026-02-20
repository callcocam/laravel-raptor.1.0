<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types\Editable;

use Closure;

class NumberEditableColumn extends BaseEditableColumn
{

    protected string|Closure|null $component = 'editable-text-input';
    
    public function getInputType(): string
    {
        return 'number';
    }
}
