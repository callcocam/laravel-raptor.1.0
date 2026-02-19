<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Navigation;

use Callcocam\LaravelRaptor\Support\Concerns\EvaluatesClosures;
use Callcocam\LaravelRaptor\Support\Concerns\FactoryPattern;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToIcon;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToLabel;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToName;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Page
{
    use EvaluatesClosures;
    use FactoryPattern;
    use BelongsToIcon;
    use BelongsToLabel;
    use BelongsToName;

    protected string $slug;

    protected ?string $controllerClass = null;

    protected Closure|string|null $urlPrefix = null;

    protected int $order = 0;

    protected ?string $group = null;

    protected ?string $groupIcon = null;

    /** @var array<string> */
    protected array $middleware = [];

    protected bool $hasCreate = false;

    protected bool $hasEdit = false;

    protected bool $hasShow = false;

    protected bool $hasDestroy = false;

    protected bool $hasForceDelete = false;

    protected bool $hasRestore = false;

    /** Segmentos de URL configuráveis (ex.: 'edit' em /{id}/edit). Closure permite tradução por locale. */
    protected Closure|string|null $createSegment = null;

    protected Closure|string|null $editSegment = null;

    protected Closure|string|null $forceDeleteSegment = null;

    protected Closure|string|null $restoreSegment = null;

    /** @var array<string, Closure|bool|null> */
    protected array $abilities = [
        'viewAny' => null,
        'view' => null,
        'create' => null,
        'update' => null,
        'delete' => null,
    ];

    public function __construct(string $slug)
    {
        $this->slug = $slug;
        $this->name = $slug;
        $this->label = Str::title(str_replace(['-', '_'], ' ', $slug));
        $this->icon = null;
    }

    public function controller(string $controllerClass): static
    {
        $this->controllerClass = $controllerClass;

        return $this;
    }

    public function url(string|Closure|null $url): static
    {
        $this->urlPrefix = $this->evaluate($url);

        return $this;
    }

    public function order(int $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function group(string $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function groupIcon(string $groupIcon): static
    {
        $this->groupIcon = $groupIcon;

        return $this;
    }

    /**
     * @param  array<string>|string  $middleware
     */
    public function middleware(array|string $middleware): static
    {
        $this->middleware = array_merge(
            $this->middleware,
            (array) $middleware
        );

        return $this;
    }

    public function create(bool $enabled = true): static
    {
        $this->hasCreate = $enabled;

        return $this;
    }

    public function edit(bool $enabled = true): static
    {
        $this->hasEdit = $enabled;

        return $this;
    }

    public function show(bool $enabled = true): static
    {
        $this->hasShow = $enabled;

        return $this;
    }

    public function destroy(bool $enabled = true): static
    {
        $this->hasDestroy = $enabled;

        return $this;
    }

    public function forceDelete(bool $enabled = true): static
    {
        $this->hasForceDelete = $enabled;

        return $this;
    }

    public function restore(bool $enabled = true): static
    {
        $this->hasRestore = $enabled;

        return $this;
    }

    public function resource(): static
    {
        $this->hasCreate = true;
        $this->hasEdit = true;
        $this->hasShow = true;
        $this->hasDestroy = true;

        return $this;
    }

    public function canViewAny(Closure|bool|null $callback = null): static
    {
        $this->abilities['viewAny'] = $callback ?? true;

        return $this;
    }

    public function canView(Closure|bool|null $callback = null): static
    {
        $this->abilities['view'] = $callback ?? true;

        return $this;
    }

    public function canCreate(Closure|bool|null $callback = null): static
    {
        $this->abilities['create'] = $callback ?? true;

        return $this;
    }

    public function canUpdate(Closure|bool|null $callback = null): static
    {
        $this->abilities['update'] = $callback ?? true;

        return $this;
    }

    public function canDelete(Closure|bool|null $callback = null): static
    {
        $this->abilities['delete'] = $callback ?? true;

        return $this;
    }

    /**
     * Segmento da URL para create (ex.: 'create' ou traduzido 'criar').
     * Closure é avaliado no momento do registro das rotas (permite __() por locale).
     */
    public function createSegment(Closure|string $segment): static
    {
        $this->createSegment = $segment;

        return $this;
    }

    /**
     * Segmento da URL para edit (ex.: 'edit' ou traduzido 'editar').
     * Closure é avaliado no momento do registro das rotas (permite __() por locale).
     */
    public function editSegment(Closure|string $segment): static
    {
        $this->editSegment = $segment;

        return $this;
    }

    /**
     * Segmento da URL para force-delete (ex.: 'force-delete' ou traduzido).
     */
    public function forceDeleteSegment(Closure|string $segment): static
    {
        $this->forceDeleteSegment = $segment;

        return $this;
    }

    /**
     * Segmento da URL para restore (ex.: 'restore' ou traduzido).
     */
    public function restoreSegment(Closure|string $segment): static
    {
        $this->restoreSegment = $segment;

        return $this;
    }

    public function registerRoutes(): void
    {
        $prefix = $this->urlPrefix ?? $this->slug;
        $name = $this->getName() ?? $this->slug;
        $controller = $this->controllerClass;

        $routeGroup = Route::prefix($prefix)->name("{$name}.");

        if (! empty($this->middleware)) {
            $routeGroup->middleware($this->middleware);
        }

        $routeGroup->group(function () use ($controller, $name) {
            Route::get('/', [$controller, 'index'])->name('index');

            if ($this->hasCreate) {
                $createSegment = $this->resolveSegment($this->createSegment, 'create');
                Route::get(sprintf('/%s', $createSegment), [$controller, 'create'])->name('create');
                Route::post('/', [$controller, 'store'])->name('store');
            }

            if ($this->hasShow) {
                Route::get('/{id}', [$controller, 'show'])->name('show');
            }

            if ($this->hasEdit) {
                $editSegment = $this->resolveSegment($this->editSegment, 'edit');
                Route::get(sprintf('/{id}/%s', $editSegment), [$controller, 'edit'])->name('edit');
                Route::put('/{id}', [$controller, 'update'])->name('update');
                Route::patch('/{id}', [$controller, 'update'])->name('update.patch');
            }

            if ($this->hasDestroy) {
                Route::delete('/{id}', [$controller, 'destroy'])->name('destroy');
            }

            if ($this->hasForceDelete) {
                $forceDeleteSegment = $this->resolveSegment($this->forceDeleteSegment, 'force-delete');
                Route::delete(sprintf('/{id}/%s', $forceDeleteSegment), [$controller, 'forceDelete'])->name('force-delete');
            }

            if ($this->hasRestore) {
                $restoreSegment = $this->resolveSegment($this->restoreSegment, 'restore');
                Route::post(sprintf('/{id}/%s', $restoreSegment), [$controller, 'restore'])->name('restore');
            }

            Route::post('/{id}/action/{actionName}', [$controller, 'executeAction'])->name('execute-action');
            Route::post('/bulk-action/{actionName}', [$controller, 'executeBulkAction'])->name('execute-bulk-action');
        });
    }

    /**
     * Verifica se o user autenticado pode ver este item no sidebar.
     * Quando viewAny é null, usa Gate com nome de rota (ex.: products.index),
     * respeitando Shinobi (hasPermissionTo por slug) e Policy como fallback.
     */
    public function isAuthorized(): bool
    {
        $ability = $this->abilities['viewAny'];

        if ($ability === null) {
            $user = Auth::user();
            if (! $user) {
                return false;
            }
            $routeAbility = $this->getRouteName().'.index';
            if (! Gate::has($routeAbility)) {
                return true;
            }

            return Gate::forUser($user)->allows($routeAbility);
        }

        if ($ability instanceof Closure) {
            return (bool) $this->evaluate($ability, [
                'user' => Auth::user(),
            ]);
        }

        if ($ability === true) {
            return $this->checkPolicy('viewAny');
        }

        return (bool) $ability;
    }

    /**
     * Verifica se uma ability especifica esta autorizada.
     */
    public function isAbilityAuthorized(string $ability): bool
    {
        $check = $this->abilities[$ability] ?? null;

        if ($check === null) {
            return true;
        }

        if ($check instanceof Closure) {
            return (bool) $this->evaluate($check, [
                'user' => Auth::user(),
            ]);
        }

        if ($check === true) {
            return $this->checkPolicy($ability);
        }

        return (bool) $check;
    }

    /**
     * @return array{label: string, href: string, icon: string|null, order: int}
     */
    public function toNavigationItem(): array
    {
        $prefix = $this->urlPrefix ?? $this->slug;

        return [
            'label' => $this->getLabel() ?? $this->slug,
            'href' => url($prefix),
            'icon' => $this->getIcon(),
            'order' => $this->order,
        ];
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getRouteName(): string
    {
        return $this->getName() ?? $this->slug;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getGroupIcon(): ?string
    {
        return $this->groupIcon;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Ícone pode ser null na navegação (trait retorna string).
     */
    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function getControllerClass(): ?string
    {
        return $this->controllerClass;
    }

    /**
     * Retorna a classe do model do controller (para registro de abilities no Gate).
     */
    public function getModelClass(): ?string
    {
        try {
            $controller = $this->controllerClass;

            if (! $controller) {
                return null;
            }

            $controllerInstance = app($controller);

            if (! property_exists($controllerInstance, 'model')) {
                return null;
            }

            $modelClass = (new \ReflectionProperty($controllerInstance, 'model'))->getValue($controllerInstance);

            if (! $modelClass) {
                return null;
            }

            return is_string($modelClass) ? $modelClass : get_class($modelClass);
        } catch (\Throwable) {
            return null;
        }
    }

    public function hasCreate(): bool
    {
        return $this->hasCreate;
    }

    public function hasEdit(): bool
    {
        return $this->hasEdit;
    }

    public function hasShow(): bool
    {
        return $this->hasShow;
    }

    public function hasDestroy(): bool
    {
        return $this->hasDestroy;
    }

    public function hasForceDelete(): bool
    {
        return $this->hasForceDelete;
    }

    public function hasRestore(): bool
    {
        return $this->hasRestore;
    }

    /**
     * Resolve segment de URL: avalia Closure ou usa valor/default.
     * Segmentos devem ser seguros para URL (sem espaços, preferir slug).
     */
    protected function resolveSegment(Closure|string|null $value, string $default): string
    {
        if ($value === null) {
            return $default;
        }

        $resolved = $this->evaluate($value);

        return is_string($resolved) && $resolved !== '' ? $resolved : $default;
    }

    protected function checkPolicy(string $ability): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        try {
            $controller = $this->controllerClass;

            if (! $controller) {
                return true;
            }

            $controllerInstance = app($controller);

            if (! property_exists($controllerInstance, 'model')) {
                return true;
            }

            $modelClass = (new \ReflectionProperty($controllerInstance, 'model'))->getValue($controllerInstance);

            if (! $modelClass) {
                return true;
            }

            $modelClass = is_string($modelClass) ? $modelClass : get_class($modelClass);

            return Gate::forUser($user)->allows($ability, $modelClass);
        } catch (\Throwable) {
            return true;
        }
    }
}
