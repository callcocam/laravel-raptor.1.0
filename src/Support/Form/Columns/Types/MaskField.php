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

class MaskField extends Column
{
    protected Closure|string|null $component = 'form-field-mask';

    protected ?string $mask = null;

    public function mask(string $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    protected function getType(): string
    {
        return 'mask';
    }

    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        $arr = array_merge(parent::toArray($model, $request), []);
        if ($this->mask !== null) {
            $arr['mask'] = $this->mask;
        }

        return $arr;
    }
}
