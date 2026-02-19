<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenant
    |--------------------------------------------------------------------------
    |
    | Ativa/desativa o suporte a multi-tenancy. Quando desabilitado, o
    | LandlordServiceProvider não é registrado e models não são scoped.
    |
    */
    'multi_tenant' => [
        'enabled' => false,
        'resolver' => \Callcocam\LaravelRaptor\Services\TenantResolver::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Landlord
    |--------------------------------------------------------------------------
    */
    'landlord' => [
        'default_tenant_columns' => ['tenant_id'],
        'subdomain' => 'landlord',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */
    'database' => [
        'landlord_connection_name' => 'landlord',
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    */
    'tables' => [
        'tenants' => 'tenants',
        'tenant_domains' => 'tenant_domains',
        'users' => 'users',
        'permissions' => 'permissions',
        'roles' => 'roles',
        'permission_role' => 'permission_role',
        'permission_user' => 'permission_user',
        'role_user' => 'role_user',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    */
    'models' => [
        'tenant' => \Callcocam\LaravelRaptor\Models\Tenant::class,
        'tenant_domain' => \Callcocam\LaravelRaptor\Models\TenantDomain::class,
    ],

    'tenant' => [
        'subdomain_column' => 'domain',
    ],

    /*
    |--------------------------------------------------------------------------
    | Shinobi (Permissions & Roles)
    |--------------------------------------------------------------------------
    */
    'shinobi' => [
        'models' => [
            'role' => \Callcocam\LaravelRaptor\Support\Shinobi\Models\Role::class,
            'permission' => \Callcocam\LaravelRaptor\Support\Shinobi\Models\Permission::class,
        ],
        'tables' => [
            'roles' => 'roles',
            'permissions' => 'permissions',
        ],
        'cache' => [
            'enabled' => false,
            'tag' => 'shinobi',
            'length' => 3600,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sluggable
    |--------------------------------------------------------------------------
    */
    'sluggable' => [
        'slug' => 'slug',
        'name' => 'name',
    ],

];
