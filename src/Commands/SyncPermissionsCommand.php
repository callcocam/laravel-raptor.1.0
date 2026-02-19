<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Commands;

use Callcocam\LaravelRaptor\Enums\PermissionStatus;
use Callcocam\LaravelRaptor\LaravelRaptor;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncPermissionsCommand extends Command
{
    public $signature = 'raptor:sync-permissions
                            {--dry-run : Lista as permissões sem criar no banco}';

    public $description = 'Sincroniza permissões no banco a partir das Pages registradas';

    /** @var array<string, string> Mapeamento ação → descrição */
    protected array $actionDescriptions = [
        'index' => 'Listar',
        'create' => 'Criar',
        'store' => 'Criar',
        'show' => 'Visualizar',
        'edit' => 'Editar',
        'update' => 'Atualizar',
        'destroy' => 'Excluir',
        'force-delete' => 'Excluir permanentemente',
        'restore' => 'Restaurar',
    ];

    public function handle(LaravelRaptor $raptor): int
    {
        $pages = $raptor->getPages();

        if (empty($pages)) {
            $this->warn('Nenhuma Page registrada. Registre Pages antes de sincronizar permissões.');

            return self::FAILURE;
        }

        $permissionModel = app(config('raptor.shinobi.models.permission'));
        $created = 0;
        $existing = 0;

        foreach ($pages as $page) {
            $routeName = $page->getRouteName();
            $label = $page->getLabel() ?? Str::title($page->getSlug());

            $actions = $this->getActionsForPage($page);

            foreach ($actions as $action) {
                $slug = "{$routeName}.{$action}";
                $description = ($this->actionDescriptions[$action] ?? Str::title($action)).' '.$label;

                if ($this->option('dry-run')) {
                    $this->line("  [dry-run] {$slug} — {$description}");
                    $created++;

                    continue;
                }

                $exists = $permissionModel->newQuery()->where('slug', $slug)->exists();

                if ($exists) {
                    $this->line("  <fg=gray>[exists]</> {$slug}");
                    $existing++;
                } else {
                    $permissionModel->newQuery()->create([
                        'name' => $description,
                        'slug' => $slug,
                        'description' => $description,
                        'status' => PermissionStatus::Published->value,
                    ]);
                    $this->line("  <fg=green>[created]</> {$slug} — {$description}");
                    $created++;
                }
            }
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info("Dry run: {$created} permissões seriam criadas.");
        } else {
            $total = $created + $existing;
            $this->info("Sincronizado: {$total} permissões ({$created} criadas, {$existing} existentes).");
        }

        return self::SUCCESS;
    }

    /**
     * @return array<string>
     */
    protected function getActionsForPage($page): array
    {
        $actions = ['index'];

        if ($page->hasCreate()) {
            $actions[] = 'create';
            $actions[] = 'store';
        }

        if ($page->hasShow()) {
            $actions[] = 'show';
        }

        if ($page->hasEdit()) {
            $actions[] = 'edit';
            $actions[] = 'update';
        }

        if ($page->hasDestroy()) {
            $actions[] = 'destroy';
        }

        if ($page->hasForceDelete()) {
            $actions[] = 'force-delete';
        }

        if ($page->hasRestore()) {
            $actions[] = 'restore';
        }

        return $actions;
    }
}
