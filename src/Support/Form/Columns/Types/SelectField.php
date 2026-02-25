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
use Illuminate\Support\Facades\Route;

class SelectField extends Column
{
    protected Closure|string|null $component = 'form-field-select';

    /** @var array<int, array{label: string, value: string|int}>|Closure */
    protected array|Closure $options = [];

    /** @var Closure|null (Request $request, ?Model $model, string $q) => array<int, array{value: string|int, label: string}> */
    protected ?Closure $searchCallback = null;

    /**
     * @param  array<int, array{label: string, value: string|int}>|Closure  $options
     */
    public function options(array|Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Define callback para busca ao digitar. Recebe (Request $request, ?Model $model, string $q) e retorna array de ['value' => ..., 'label' => ...].
     *
     * @param  Closure(Request, ?Model, string): array<int, array{value: string|int, label: string}>  $callback
     */
    public function searchUsing(Closure $callback): static
    {
        $this->searchCallback = $callback;

        return $this;
    }

    /**
     * @return Closure(Request, ?Model, string): array<int, array{value: string|int, label: string}>|null
     */
    public function getSearchCallback(): ?Closure
    {
        return $this->searchCallback;
    }

    /**
     * @return array<int, array{label: string, value: string|int}>
     */
    public function getOptions(?Model $model = null, ?Request $request = null): array
    {
        $evaluated = $this->evaluate($this->options, [
            'model' => $model,
            'request' => $request,
        ]);

        return is_array($evaluated) ? $evaluated : [];
    }

    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        $data = array_merge(parent::toArray($model, $request), [
            'options' => $this->getOptions($model, $request),
        ]);

        if ($this->getSearchCallback() !== null && $request !== null && $request->route() !== null) {
            $routeName = $request->route()->getName();
            $prefix = explode('.', $routeName)[0] ?? null;
            if ($prefix !== null && Route::has("{$prefix}.search-field")) {
                $data['component'] = 'form-field-combobox';
                $data['searchExecuteUrl'] = route("{$prefix}.search-field", ['fieldName' => $this->getName()]);
            }
        }

        return $data;
    }
}
