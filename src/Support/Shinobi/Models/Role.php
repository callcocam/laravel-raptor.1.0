<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Shinobi\Models;

use Callcocam\LaravelRaptor\Models\AbstractModel;
use Callcocam\LaravelRaptor\Support\Shinobi\Concerns\HasPermissions;
use Callcocam\LaravelRaptor\Support\Shinobi\Contracts\Role as RoleContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends AbstractModel implements RoleContract
{
    use HasFactory, HasPermissions, SoftDeletes;

    protected static function newFactory(): \Callcocam\LaravelRaptor\Database\Factories\RoleFactory
    {
        return \Callcocam\LaravelRaptor\Database\Factories\RoleFactory::new();
    }

    /**
     * Roles são globais — não participam do tenant scoping.
     */
    public bool $ignoreTenant = true;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('raptor.shinobi.tables.roles', 'roles'));
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('auth.model') ?: config('auth.providers.users.model'))->withTimestamps();
    }

    /**
     * Determine if role has permission flags.
     * special: true = all-access, false = no-access, null = normal (check permissions).
     */
    public function hasPermissionFlags(): bool
    {
        return $this->special !== null;
    }

    /**
     * Determine if the requested permission is permitted or denied
     * through a special role flag.
     * Aceita boolean (true = all-access, false = no-access) ou string legada.
     */
    public function hasPermissionThroughFlag(): bool
    {
        if (! $this->hasPermissionFlags()) {
            return true;
        }

        if ($this->special === false || $this->special === 'no-access') {
            return false;
        }

        return true;
    }
}
