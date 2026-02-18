<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Sources\Modifiers;

use Callcocam\LaravelRaptor\Support\Table\TableQueryContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Cada modifier é uma etapa isolada do pipeline de query.
 * A DatabaseSource executa todos em sequência.
 */
interface QueryModifier
{
    public function apply(Builder $query, Request $request, TableQueryContext $context): Builder;
}
