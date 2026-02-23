<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form;

use Callcocam\LaravelRaptor\Support\AbstractColumn;
use Callcocam\LaravelRaptor\Support\Actions\AbstractAction;
use Callcocam\LaravelRaptor\Support\Concerns\EvaluatesClosures;
use Callcocam\LaravelRaptor\Support\Concerns\HasGridLayout;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithColumns;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithFooterActions;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithHeaderActions;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongToRequest;
use Callcocam\LaravelRaptor\Support\Form\Columns\Types\RepeaterField;
use Callcocam\LaravelRaptor\Support\Form\Columns\Types\SectionField;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormBuilder
{
    use EvaluatesClosures;
    use HasGridLayout;
    use WithColumns;
    use WithFooterActions;
    use WithHeaderActions;
    use BelongToRequest;

    /**
     * @var Closure|array<int, AbstractColumn>
     */
    protected Closure|array $columns = [];

    protected ?string $submitUrl = null;

    protected string $submitMethod = 'post';

    protected array $formComponents = [
        'renderer' => 'form-renderer',
    ];

    /** @var array<string, mixed> Usado pelo trait WithColumns (setValue). Valores do form vêm de getFormValues() dos fields. */
    protected array $values = [];

    public function __construct(Request $request, protected ?Model $model = null)
    {
        $this->request($request);
    }

    public function submitUrl(string $url): static
    {
        $this->submitUrl = $url;

        return $this;
    }

    public function submitMethod(string $method): static
    {
        $this->submitMethod = strtolower($method);

        return $this;
    }

    public function formComponents(array $components): static
    {
        foreach ($components as $name => $component) {
            $this->formComponent($name, $component);
        }

        return $this;
    }

    public function formComponent(string $name, string $component): static
    {
        $this->formComponents[$name] = $component;

        return $this;
    }

    public function getFormComponents(): array
    {
        return $this->formComponents;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $fields = $this->buildFields($this->getResolvedFields());
        $payload = [
            'fields' => $fields,
            'components' => $this->getFormComponents(),
        ];
        if ($this->model !== null) {
            $payload['values'] = $this->buildModelValues();
            $payload['model'] = $payload['values'];
        }
        if ($this->submitUrl !== null) {
            $payload['submitUrl'] = $this->submitUrl;
            $payload['submitMethod'] = $this->submitMethod;
        }

        $payload['headerActions'] = $this->renderFormActions($this->getHeaderActions());
        $payload['footerActions'] = $this->renderFormActions($this->getFooterActions());

        $gridConfig = $this->getGridLayoutConfig();
        if ($gridConfig !== []) {
            $payload['gridLayout'] = $gridConfig;
        }
        Storage::disk('local')->put('payload-form.json', json_encode($payload, JSON_PRETTY_PRINT));
        return $payload;
    }

    /**
     * @param  AbstractAction[]  $actions
     * @return array<int, array<string, mixed>>
     */
    protected function renderFormActions(array $actions): array
    {
        $rendered = [];
        foreach ($actions as $action) {
            $result = $action->render($this->model, $this->request);
            if ($result !== null) {
                $rendered[] = $result;
            }
        }

        return $rendered;
    }

    /**
     * @return array<int, AbstractColumn|SectionField|RepeaterField>
     */
    protected function getResolvedFields(): array
    {
        $evaluated = $this->getColumns($this->model, $this->getRequest());
        if (! is_array($evaluated)) {
            return [];
        }

        return array_values($evaluated);
    }

    /**
     * Retorna o RepeaterField pelo nome, ou null se não existir.
     */
    public function getRepeaterField(string $name): ?RepeaterField
    {
        foreach ($this->getResolvedFields() as $column) {
            if ($column instanceof RepeaterField && $column->getName() === $name) {
                return $column;
            }
        }

        return null;
    }

    /**
     * Um único array de itens: cada item é um campo (toArray) ou uma section (toArray com type section).
     * Cada field é responsável por seu próprio toArray(); o form não trata dados aqui.
     *
     * @param  array<int, AbstractColumn|SectionField>  $resolved
     * @return array<int, array<string, mixed>>
     */
    protected function buildFields(array $resolved): array
    {
        $fields = [];
        foreach ($resolved as $column) {
            $fields[] = $column->toArray($this->getModel(), $this->getRequest());
        }

        return $fields;
    }

    /**
     * Valores do model para o payload. Cada field contribui com getFormValues(); o form só agrega.
     *
     * @return array<string, mixed>
     */
    protected function buildModelValues(): array
    {
        $values = $this->model->toArray();
        foreach ($this->getResolvedFields() as $column) {
            if (method_exists($column, 'getFormValues')) {
                $values = array_replace_recursive($values, $column->getFormValues($this->model, $this->getRequest()));
            }
        }

        return $values;
    }
}
