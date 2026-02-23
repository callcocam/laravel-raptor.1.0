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
 * Action de item do repeater executada no backend.
 * O callback recebe: request, model do form, nome do repeater, índice do item e dados do item.
 * Frontend faz POST no executeUrl com body: { index: number, item: object }.
 */
class RepeaterItemCallbackAction extends AbstractAction
{
    protected Closure|string|null $component = 'callback-action';

    /** @var Closure|null (Request $request, ?Model $model, string $repeaterName, int $itemIndex, array $itemData): mixed */
    protected ?Closure $executeCallback = null;

    public function executeUsing(Closure $callback): static
    {
        $this->executeCallback = $callback;

        return $this;
    }

    /**
     * Executa a action no backend (chamado pelo controller).
     *
     * @param  array<string, mixed>  $itemData
     */
    public function execute(Request $request, ?Model $model, string $repeaterName, int $itemIndex, array $itemData): mixed
    {
        if ($this->executeCallback === null) {
            return null;
        }

        $result = $this->evaluate($this->executeCallback, [
            'request' => $request,
            'model' => $model,
            'repeaterName' => $repeaterName,
            'itemIndex' => $itemIndex,
            'itemData' => $itemData,
            'item' => $itemData,
        ]);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return $result;
    }

    /**
     * Payload da action para o frontend (por item do repeater), com executeUrl.
     */
    public function renderForRepeaterItem(string $repeaterName, ?Model $model, ?Request $request): array
    {
        $base = parent::render($model, $request);
        if ($base === null) {
            $base = $this->toArray($model, $request);
        }

        $data = array_merge($base, [
            'actionName' => $this->getName(),
            'executeUrl' => $this->resolveRepeaterItemExecuteUrl($repeaterName, $model, $request),
        ]);

        return $data;
    }

    protected function resolveRepeaterItemExecuteUrl(string $repeaterName, ?Model $model, ?Request $request): string
    {
        if ($request === null) {
            return '';
        }

        $routeName = $request->route()?->getName();
        if ($routeName) {
            $prefix = explode('.', $routeName)[0];
            $id = $model?->getKey() ?? '0';

            return '/'.$prefix.'/'.$id.'/repeater-action/'.$repeaterName.'/'.$this->getName();
        }

        $currentUrl = $request->url();
        $baseUrl = preg_replace('/\/[^\/]+$/', '', $currentUrl);

        return $baseUrl.'/repeater-action/'.$repeaterName.'/'.$this->getName();
    }
}
