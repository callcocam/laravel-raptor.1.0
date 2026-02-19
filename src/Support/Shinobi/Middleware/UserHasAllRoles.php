<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Shinobi\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class UserHasAllRoles
{
    public function __construct(protected Guard $auth) {}

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function handle($request, Closure $next, string ...$roles): mixed
    {
        if (! $this->auth->user()?->hasAllRoles(...$roles)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }

            abort(403);
        }

        return $next($request);
    }
}
