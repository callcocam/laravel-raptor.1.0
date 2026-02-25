<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form\Columns\Types;

use Callcocam\LaravelRaptor\Support\Form\Columns\Column;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ComboboxField extends Column
{
    protected Closure|string|null $component = 'form-field-combobox';

    protected Closure|string|null $model = null;

    protected string $searchField = 'name';

    protected string $searchValue = 'id';

    protected string $searchLabel = 'name';

    protected string $searchWhere = 'like';

    public function model(Closure|string|null $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): Model|string|null
    {
        return $this->evaluate($this->model) ?? null;
    }

    public function searchField(string $searchField): static
    {
        $this->searchField = $searchField;

        return $this;
    }

    public function getSearchField(): string
    {
        return $this->searchField;
    }

    public function searchValue(string $searchValue): static
    {
        $this->searchValue = $searchValue;

        return $this;
    }

    public function getSearchValue(): string
    {
        return $this->searchValue;
    }

    public function searchLabel(string $searchLabel): static
    {
        $this->searchLabel = $searchLabel;

        return $this;
    }

    public function getSearchLabel(): string
    {
        return $this->searchLabel;
    }

    public function searchWhere(string $searchWhere): static
    {
        $this->searchWhere = $searchWhere;

        return $this;
    }

    public function getSearchWhere(): string
    {
        return $this->searchWhere;
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


    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        $this->options(function (Request $request) use ($model) {

            $modelClass = $this->getModel();
            if ($modelClass === null) {
                return [];
            }
            $search = $request->input('q');
            $options =  $modelClass::query()
                ->where($this->getSearchValue(), data_get($model, $this->getName()))
                ->where($this->getSearchField(), $this->getSearchWhere(), "%{$search}%")->limit(20)->get()->map(function (Model $model) {
                    return ['value' => $model->{$this->getSearchValue()}, 'label' => $model->{$this->getSearchLabel()}];
                })->toArray();
            return $options;
        });
        $data = array_merge(parent::toArray($model, $request), [
            'options' => $this->getOptions($model, $request),
        ]);

        return $data;
    }
}
