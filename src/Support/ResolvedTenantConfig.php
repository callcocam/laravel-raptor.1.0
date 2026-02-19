<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * DTO que encapsula a configuração resolvida de um tenant.
 *
 * A conexão "landlord" nunca muda — sempre aponta para o banco principal.
 * A conexão "default" é alterada para o banco do tenant quando preenchido.
 *
 * O domainData (domainable_type + domainable_id) permite resolver
 * sub-entidades do tenant (Client, Store, etc.) via polimorfismo.
 */
class ResolvedTenantConfig
{
    public function __construct(
        public readonly Model $tenant,
        public readonly ?string $database,
        public readonly ?string $domainableType,
        public readonly ?string $domainableId,
    ) {}

    /**
     * Cria instância a partir do model tenant e dados opcionais do domínio.
     *
     * @param  object|null  $domainData  Objeto com domainable_type e domainable_id (da tabela tenant_domains)
     */
    public static function from(Model $tenant, ?object $domainData = null): self
    {
        return new self(
            tenant: $tenant,
            database: $tenant->database ?? null,
            domainableType: $domainData->domainable_type ?? null,
            domainableId: isset($domainData->domainable_id) ? (string) $domainData->domainable_id : null,
        );
    }

    /**
     * Retorna o nome do banco a usar na conexão default.
     * Se o tenant tem database próprio, usa-o; senão, mantém o default.
     */
    public function getDatabaseName(): ?string
    {
        return $this->database;
    }

    /**
     * Verifica se o tenant usa banco dedicado.
     */
    public function hasDedicatedDatabase(): bool
    {
        return $this->database !== null && $this->database !== '';
    }

    /**
     * Verifica se existe sub-entidade resolvida (Client, Store, etc.).
     */
    public function hasDomainable(): bool
    {
        return $this->domainableType !== null && $this->domainableId !== null;
    }

    /**
     * Retorna configurações para aplicar via config().
     *
     * @return array<string, mixed>
     */
    public function toAppConfig(): array
    {
        $config = [
            'app.tenant_id' => $this->tenant->getKey(),
            'app.context' => 'tenant',
        ];

        if ($this->hasDomainable()) {
            $config['app.domainable_type'] = $this->domainableType;
            $config['app.domainable_id'] = $this->domainableId;
        }

        return $config;
    }
}
