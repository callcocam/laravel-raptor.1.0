<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Models;

use Callcocam\LaravelRaptor\Support\Landlord\UsesLandlordConnection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $domain
 * @property bool $is_primary
 * @property string $tenant_id
 * @property string|null $domainable_type
 * @property string|null $domainable_id
 */
class TenantDomain extends AbstractModel
{
    use UsesLandlordConnection;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('raptor.tables.tenant_domains', 'tenant_domains'));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            config('raptor.models.tenant', Tenant::class),
            'tenant_id'
        );
    }

    /**
     * Relação polimórfica (Client, Store, etc.).
     */
    public function domainable(): MorphTo
    {
        return $this->morphTo();
    }
}
