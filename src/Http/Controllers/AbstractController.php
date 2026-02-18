<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Http\Controllers;

use Callcocam\LaravelRaptor\Support\Form\FormBuilder;
use Callcocam\LaravelRaptor\Support\Info\InfoBuilder;
use Callcocam\LaravelRaptor\Support\Table\TableBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;

class AbstractController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * The model to be used in the controller.
     */
    protected Model|string|null $model = null;

    /**
     * The table builder to be used in the controller.
     */
    protected ?string $tableBuilder = null;

    /**
     * The form builder to be used in the controller.
     */
    protected ?string $formBuilder = null;

    /**
     * The info builder to be used in the controller.
     */
    protected ?string $infoBuilder = null;

    /**
     * Get the model to be used in the controller.
     */
    protected function getModel(): Model
    {
        return $this->model instanceof Model ? $this->model : app($this->model);
    }

    protected function getIndexPage(): string
    {
        return 'crud/index';
    }

    protected function getCreatePage(): string
    {
        return 'crud/create';
    }

    protected function getEditPage(): string
    {
        return 'crud/edit';
    }

    protected function getShowPage(): string
    {
        return 'crud/show';
    }

    protected function table(TableBuilder $table): TableBuilder
    {
        return $table;
    }

    protected function form(FormBuilder $form): FormBuilder
    {
        return $form;
    }

    protected function info(InfoBuilder $info): InfoBuilder
    {
        return $info;
    }

    protected function getTableBuilder(Request $request): ?TableBuilder
    {
        if ($this->tableBuilder) {
            return app($this->tableBuilder, ['request' => $request, 'model' => $this->getModel()]);
        }

        return new TableBuilder($request, $this->getModel());
    }

    protected function getFormBuilder(Request $request): ?FormBuilder
    {
        if ($this->formBuilder) {
            return app($this->formBuilder, ['request' => $request]);
        }

        return new FormBuilder($request);
    }

    protected function getInfoBuilder(Request $request): ?InfoBuilder
    {
        if ($this->infoBuilder) {
            return app($this->infoBuilder, ['request' => $request]);
        }

        return new InfoBuilder($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @return InertiaResponse
     */
    public function index(Request $request)
    {
        return Inertia::render($this->getIndexPage(), [
            'table' => $this->table($this->getTableBuilder($request))->render(),
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render($this->getCreatePage(), [
            'form' => $this->form($this->getFormBuilder($request))->render(),
        ]);
    }

    public function edit(Request $request, string $id)
    {
        return Inertia::render($this->getEditPage(), [
            'id' => $id,
            'form' => $this->form($this->getFormBuilder($request))->render(),
        ]);
    }

    public function show(Request $request, string $id)
    {
        return Inertia::render($this->getShowPage(), [
            'id' => $id,
            'info' => $this->info($this->getInfoBuilder($request))->render(),
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $model = $this->getModel()->find($id);
        if ($model) {
            $model->delete();
        }

        return redirect()->route($this->getIndexPage());
    }

    public function store(Request $request)
    {
        $model = $this->getModel()->create($request->all());

        return redirect()->route($this->getIndexPage());
    }

    public function update(Request $request, string $id)
    {
        $model = $this->getModel()->find($id);
        if ($model) {
            $model->update($request->all());
        }

        return redirect()->route($this->getIndexPage());
    }
}
