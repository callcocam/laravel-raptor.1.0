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
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FormBuilder
{
    use EvaluatesClosures;
    use HasGridLayout;
    use WithColumns;
    use WithFooterActions;
    use WithHeaderActions;

    /**
     * @var Closure|array<int, AbstractColumn>
     */
    protected Closure|array $columns = [];

    protected ?string $submitUrl = null;

    protected string $submitMethod = 'post';

    protected array $formComponents = [
        'renderer' => 'form-renderer',
    ];

    public function __construct(protected Request $request, protected ?Model $model = null) {}

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
        $resolvedFields = $this->getResolvedFields();

        $payload = [
            'fields' => array_map(fn (AbstractColumn $f) => $f->toArray(), $resolvedFields),
            'components' => $this->getFormComponents(),
            'values' => $this->buildValues($resolvedFields),
        ];

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
     * @return array<int, AbstractColumn>
     */
    protected function getResolvedFields(): array
    {
        $evaluated = $this->getColumns($this->model);
        if (! is_array($evaluated)) {
            return [];
        }

        return array_values($evaluated);
    }

    /**
     * @param  array<int, AbstractColumn>  $fields
     * @return array<string, mixed>
     */
    protected function buildValues(array $fields): array
    {
        if ($this->model === null) {
            return [];
        }

        $values = [];
        foreach ($fields as $field) {
            $name = $field->getName();
            $values[$name] = $this->model->getAttribute($name);
        }

        return $values;
    }
}
