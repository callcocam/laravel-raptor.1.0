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

    /** @var array<int, array{label: string, value: string|int}>|Closure */
    protected array|Closure $options = [];

    /**
     * @param  array<int, array{label: string, value: string|int}>|Closure  $options
     */
    public function options(array|Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array<int, array{label: string, value: string|int}>
     */
    public function getOptions(): array
    {
        $evaluated = $this->evaluate($this->options);

        return is_array($evaluated) ? $evaluated : [];
    }

    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        return array_merge(parent::toArray($model, $request), [
            'options' => $this->getOptions(),
        ]);
    }
}
