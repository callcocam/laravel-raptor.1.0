<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor;

use Callcocam\LaravelRaptor\Support\Navigation\Page;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LaravelRaptor
{
    /** @var array<string, string> */
    protected array $controllers = [];

    /** @var array<Page> */
    protected array $pages = [];

    public function __construct(protected Application $app)
    {
        $this->registerController(app()->getNamespace().'Http\Controllers', app_path('Http/Controllers'));
    }

    public function registerController(string $namespace, string $controllerPath): static
    {
        $this->controllers[$namespace] = $controllerPath;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * Registra multiplas pages e cria as rotas automaticamente.
     *
     * @param  array<Page>  $pages
     */
    public function pages(array $pages): static
    {
        foreach ($pages as $page) {
            $this->addPage($page);
        }

        return $this;
    }

    /**
     * Registra pages a partir de controllers que implementam getPage() ou getPages().
     * Útil para centralizar a definição da página no próprio controller (ex.: futuro command CRUD).
     *
     * @param  array<class-string>  $controllerClasses
     */
    public function registerPagesFrom(array $controllerClasses): static
    {
        foreach ($controllerClasses as $controllerClass) {
            $controller = $this->app->make($controllerClass);

            if (! method_exists($controller, 'getPages') && ! method_exists($controller, 'getPage')) {
                continue;
            }

            $pages = method_exists($controller, 'getPages')
                ? $controller->getPages()
                : ($controller->getPage() !== null ? [$controller->getPage()] : []);

            foreach ($pages as $page) {
                $this->addPage($page);
            }
        }

        return $this;
    }

    public function addPage(Page $page): static
    {
        $this->pages[$page->getSlug()] = $page;
        $page->registerRoutes();
        $this->registerRouteAbilities($page);

        return $this;
    }

    /**
     * Registra abilities no Gate por nome de rota (ex.: products.index).
     * O Shinobi (Gate::before) verifica primeiro hasPermissionTo($ability) por slug;
     * quando a permissão não existe ou o user não tem, este fallback delega à Policy.
     */
    protected function registerRouteAbilities(Page $page): void
    {
        $routeName = $page->getRouteName();
        $modelClass = $page->getModelClass();

        if ($modelClass === null) {
            return;
        }

        $define = function (string $ability, string $policyAbility, bool $needsModel = false) use ($routeName, $modelClass) {
            $key = "{$routeName}.{$ability}";
            if (Gate::has($key)) {
                return;
            }
            if ($needsModel) {
                Gate::define($key, fn ($user, $model = null) => $model !== null && Gate::forUser($user)->allows($policyAbility, $model));
            } else {
                Gate::define($key, fn ($user) => Gate::forUser($user)->allows($policyAbility, $modelClass));
            }
        };

        $define('index', 'viewAny');
        $define('create', 'create');
        $define('store', 'create');
        $define('show', 'view', true);
        $define('edit', 'update', true);
        $define('update', 'update', true);
        $define('destroy', 'delete', true);
        $define('force-delete', 'forceDelete', true);
        $define('restore', 'restore', true);
    }

    /**
     * @return array<Page>
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    public function getPage(string $slug): ?Page
    {
        return $this->pages[$slug] ?? null;
    }

    public function clearPages(): static
    {
        $this->pages = [];

        return $this;
    }

    /**
     * Constroi a estrutura de navegacao agrupada, filtrando por autorizacao.
     *
     * @return array<int, array{label: string, icon: string|null, order: int, items: array}>
     */
    public function buildNavigation(?Request $request = null): array
    {
        $grouped = [];
        $ungrouped = [];

        foreach ($this->pages as $page) {
            if (! $page->isAuthorized()) {
                continue;
            }

            $item = $page->toNavigationItem();

            $groupName = $page->getGroup();

            if ($groupName) {
                if (! isset($grouped[$groupName])) {
                    $grouped[$groupName] = [
                        'label' => $groupName,
                        'icon' => $page->getGroupIcon(),
                        'order' => $page->getOrder(),
                        'items' => [],
                    ];
                }

                $grouped[$groupName]['items'][] = $item;

                if ($page->getOrder() < $grouped[$groupName]['order']) {
                    $grouped[$groupName]['order'] = $page->getOrder();
                }

                if (! $grouped[$groupName]['icon'] && $page->getGroupIcon()) {
                    $grouped[$groupName]['icon'] = $page->getGroupIcon();
                }
            } else {
                $ungrouped[] = [
                    'label' => $item['label'],
                    'icon' => $item['icon'],
                    'order' => $item['order'],
                    'items' => [$item],
                ];
            }
        }

        foreach ($grouped as &$group) {
            usort($group['items'], fn (array $a, array $b) => $a['order'] <=> $b['order']);
        }

        $allGroups = array_merge(array_values($ungrouped), array_values($grouped));

        usort($allGroups, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        return $allGroups;
    }
}
