<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Shinobi\Concerns;

trait RefreshesPermissionCache
{
    public static function bootRefreshesPermissionCache(): void
    {
        static::saved(function () {
            static::flushPermissionCache();
        });

        static::deleted(function () {
            static::flushPermissionCache();
        });
    }

    public static function flushPermissionCache(): void
    {
        if (! config('raptor.shinobi.cache.enabled')) {
            return;
        }

        $tag = config('raptor.shinobi.cache.tag', 'shinobi');

        cache()->tags($tag)->flush();
    }
}
