<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form\Columns\Types;

use Callcocam\LaravelRaptor\Support\Form\Columns\Column;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SelectField extends Column
{
    protected Closure|string|null $component = 'form-field-select';
 
    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        return array_merge(parent::toArray($model, $request), [
            'options' => $this->getOptions(),
        ]);
    }
}
