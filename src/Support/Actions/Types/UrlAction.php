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
use Illuminate\Http\Request;

/**
 * Action que navega para uma URL ou rota.
 * Suporta navegação via Inertia (router.visit) ou link normal (window.open).
 */
class UrlAction extends AbstractAction
{
    protected Closure|string|null $component = 'url-action';

    protected Closure|string|null $url = null;

    protected ?string $routeName = null;

    protected Closure|array|null $routeParams = null;

    protected bool $useInertia = true;

    protected string $target = '_self';

    protected string $method = 'get';

    /**
     * URL fixa ou closure que recebe o Model e retorna a URL.
     */
    public function url(Closure|string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Gera URL a partir de named route.
     */
    public function route(string $name, Closure|array|null $params = null): static
    {
        $this->routeName = $name;
        $this->routeParams = $params;

        return $this;
    }

    /**
     * Usar Inertia router.visit() (default) ou window.open/location.
     */
    public function inertia(bool $value = true): static
    {
        $this->useInertia = $value;

        return $this;
    }

    public function target(string $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function method(string $method): static
    {
        $this->method = strtolower($method);

        return $this;
    }

    public function render(?Model $model = null, ?Request $request = null): ?array
    {
        $base = parent::render($model, $request);
        if ($base === null) {
            return null;
        }

        return array_merge($base, [
            'url' => $model ? $this->resolveUrl($model) : null,
            'inertia' => $this->useInertia,
            'target' => $this->target,
            'method' => $this->method,
        ]);
    }

    protected function resolveUrl(Model $model): ?string
    {
        if ($this->routeName !== null) {
            $params = $this->routeParams instanceof Closure
                ? ($this->routeParams)($model)
                : ($this->routeParams ?? []);

            return route($this->routeName, $params);
        }

        if ($this->url instanceof Closure) {
            return ($this->url)($model);
        }

        return $this->url;
    }
}
