<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form\Columns\Types;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithColumns;
use Callcocam\LaravelRaptor\Support\Form\Columns\Column;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * BuscaCepField - Campo de busca de CEP com preenchimento automático.
 *
 * Integra com API ViaCEP. O valor do campo é um objeto com os subcampos (zip_code, street, city, etc.).
 *
 * @example
 * BuscaCepField::make('address')
 *     ->label('Endereço')
 *     ->fieldMapping([
 *         'logradouro' => 'street',
 *         'bairro' => 'district',
 *         'localidade' => 'city',
 *         'uf' => 'state',
 *     ])
 *     ->fields([...])
 */
class BuscaCepField extends Column
{
    use WithColumns;

    /** @var Closure|array<int, AbstractColumn> */
    protected Closure|array $columns = [];

    protected Closure|string|null $component = 'form-field-busca-cep';

    /** @var array<string, string> apiField => formField */
    protected array $fieldMapping = [];

    protected string $executeOnChangeField = 'zip_code';

    public function __construct(string $name, ?string $label = null)
    {
        parent::__construct($name, $label);
        $this->setDefaultMapping();
        $this->setDefaultFields();
    }

    protected function setDefaultMapping(): void
    {
        $this->fieldMapping = [
            'cep' => 'zip_code',
            'logradouro' => 'street',
            'bairro' => 'district',
            'localidade' => 'city',
            'uf' => 'state',
            'complemento' => 'complement',
        ];
    }

    protected function setDefaultFields(): void
    {
        $this->columns = [
            TextField::make('zip_code')->label('CEP')->placeholder('Digite o CEP')->columnSpan('4'),
            TextField::make('street')->label('Rua')->placeholder('Rua, avenida, etc.')->columnSpan('6'),
            TextField::make('number')->label('Número')->placeholder('Nº')->columnSpan('2'),
            TextField::make('complement')->label('Complemento')->placeholder('Apto, bloco, etc.')->columnSpan('6'),
            TextField::make('district')->label('Bairro')->placeholder('Bairro')->columnSpan('6'),
            TextField::make('city')->label('Cidade')->placeholder('Cidade')->columnSpan('8'),
            TextField::make('state')->label('UF')->placeholder('UF')->columnSpan('4'),
        ];
    }

    /**
     * Define os campos do bloco (alias de columns).
     *
     * @param  Closure|array<int, AbstractColumn>  $fields
     */
    public function fields(Closure|array $fields): static
    {
        $this->columns = $fields;

        return $this;
    }

    public function executeOnChange(string $fieldName): static
    {
        $this->executeOnChangeField = $fieldName;

        return $this;
    }

    public function getExecuteOnChange(): string
    {
        return $this->executeOnChangeField;
    }

    /**
     * Mapeamento API ViaCEP → campos do form.
     *
     * @param  array<string, string>  $mapping
     */
    public function fieldMapping(array $mapping): static
    {
        $this->fieldMapping = array_merge($this->fieldMapping, $mapping);

        return $this;
    }

    public function getFieldMapping(): array
    {
        return $this->fieldMapping;
    }

    protected function getType(): string
    {
        return 'busca-cep';
    }

    /**
     * Valor do bloco para o form: um único objeto com todos os subcampos.
     */
    public function getFormValues(?Model $model = null, ?Request $request = null): array
    {
        $value = $this->getValueUsing($request, $model);
        if (is_array($value)) {
            return [$this->getName() => $value];
        }
        if ($model !== null) {
            $nested = $model->getAttribute($this->getName());
            if (is_array($nested)) {
                return [$this->getName() => $nested];
            }
            if (is_object($nested)) {
                return [$this->getName() => $nested->toArray()];
            }
        }
        if ($this->hasDefaultUsing()) {
            $default = $this->getDefaultUsing($request, $model);

            return [$this->getName() => is_array($default) ? $default : []];
        }

        return [$this->getName() => []];
    }

    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        $children = $this->getColumns($model, $request);
        $children = is_array($children) ? $children : [];

        $arr = array_merge(parent::toArray($model, $request), [
            'type' => 'busca-cep',
            'fieldMapping' => $this->fieldMapping,
            'executeOnChange' => $this->executeOnChangeField,
            'fields' => array_map(fn (AbstractColumn $col) => $col->toArray($model, $request), $children),
        ]);
        if ($this->getGridColumns() !== null) {
            $arr['gridColumns'] = $this->getGridColumns();
        }
        if ($this->getGap() !== null) {
            $arr['gap'] = $this->getGap();
        }

        return $arr;
    }
}
