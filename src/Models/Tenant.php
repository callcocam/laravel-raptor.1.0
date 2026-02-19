<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Models;

use Callcocam\LaravelRaptor\Enums\TenantStatus;
use Callcocam\LaravelRaptor\Support\Landlord\UsesLandlordConnection;
use Callcocam\LaravelRaptor\Support\Sluggable\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property string $slug
 * @property string|null $domain
 * @property string|null $database
 * @property string|null $prefix
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $document
 * @property string|null $logo
 * @property array|null $settings
 * @property TenantStatus $status
 * @property bool $is_primary
 * @property string|null $description
 */
class Tenant extends AbstractModel
{
    use HasSlug, SoftDeletes, UsesLandlordConnection;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('raptor.tables.tenants', 'tenants'));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'status' => TenantStatus::class,
            'is_primary' => 'boolean',
        ];
    }

    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class, 'tenant_id');
    }

    /**
     * Verifica se o tenant tem banco dedicado.
     */
    public function hasDedicatedDatabase(): bool
    {
        return $this->database !== null && $this->database !== '';
    }
}
