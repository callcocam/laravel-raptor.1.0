<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Types;

use Callcocam\LaravelRaptor\Support\Actions\AbstractAction;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Action que executa uma callback no backend.
 * Frontend faz router.post(executeUrl) via Inertia.
 */
class CallbackAction extends AbstractAction
{
    protected Closure|string|null $component = 'callback-action';

    protected ?Closure $executeCallback = null;

    protected ?Closure $afterExecute = null;

    public function executeUsing(Closure $callback): static
    {
        $this->executeCallback = $callback;

        return $this;
    }

    /**
     * Callback executado após a action principal (ex: flash message, redirect custom).
     */
    public function after(Closure $callback): static
    {
        $this->afterExecute = $callback;

        return $this;
    }

    /**
     * Executa a action no backend (chamado pelo AbstractController::executeAction).
     */
    public function execute(Model $model, Request $request): mixed
    {
        if ($this->executeCallback === null) {
            return null;
        } 
        $result = $this->evaluate($this->executeCallback, [
            'model' => $model,
            'request' => $request,
        ]);

        if ($this->afterExecute !== null) {
            return ($this->afterExecute)($model, $request, $result);
        }

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return $result;
    }

    public function render(?Model $model = null, ?Request $request = null): ?array
    {
        $base = parent::render($model, $request);
        if ($base === null) {
            return null;
        }

        $data = array_merge($base, [
            'actionName' => $this->getName(),
        ]);

        if ($request !== null) {
            $data['executeUrl'] = $model !== null
                ? $this->resolveExecuteUrl($model, $request)
                : $this->resolveBulkExecuteUrl($request);
        }

        return $data;
    }

    protected function resolveExecuteUrl(Model $model, Request $request): string
    {
        $currentUrl = $request->url();
        $baseUrl = preg_replace('/\/[^\/]+$/', '', $currentUrl);

        return $baseUrl.'/'.$model->getKey().'/action/'.$this->getName();
    }

    protected function resolveBulkExecuteUrl(Request $request): string
    {
        $currentUrl = $request->url();
        // Remove query params
        $baseUrl = preg_replace('/\?.*/', '', $currentUrl);
        // Remove trailing slash if present
        $baseUrl = rtrim($baseUrl, '/');

        return $baseUrl.'/bulk-action/'.$this->getName();
    }
}
