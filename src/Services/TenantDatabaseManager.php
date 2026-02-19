<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Services;

use Callcocam\LaravelRaptor\Support\ResolvedTenantConfig;
use Illuminate\Support\Facades\DB;

/**
 * Gerencia a troca de banco de dados na conexão default.
 *
 * A conexão "landlord" nunca é alterada — sempre aponta para o banco principal.
 * Quando o tenant tem banco dedicado (tenant.database preenchido),
 * a conexão "default" é reconfigurada para apontar para esse banco.
 */
class TenantDatabaseManager
{
    /**
     * Aplica a configuração de banco do tenant na conexão default.
     * Se o tenant não tem banco dedicado, não faz nada.
     */
    public function applyConfig(ResolvedTenantConfig $config): void
    {
        if (! $config->hasDedicatedDatabase()) {
            return;
        }

        $defaultConnection = config('database.default');

        config([
            "database.connections.{$defaultConnection}.database" => $config->getDatabaseName(),
        ]);

        DB::purge($defaultConnection);
        DB::reconnect($defaultConnection);
    }

    /**
     * Restaura a conexão default para o banco original (config base).
     */
    public function resetToDefault(): void
    {
        $defaultConnection = config('database.default');
        $originalDatabase = env('DB_DATABASE');

        config([
            "database.connections.{$defaultConnection}.database" => $originalDatabase,
        ]);

        DB::purge($defaultConnection);
        DB::reconnect($defaultConnection);
    }
}
