<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support;

use Closure;
use Illuminate\Support\Str;
use Callcocam\LaravelRaptor\Support\Concerns;

abstract class AbstractColumn
{
    use Concerns\EvaluatesClosures;
    use Concerns\FactoryPattern;
    use Concerns\Shared\BelongsToLabel;
    use Concerns\Shared\BelongsToName;

    protected Closure|array $column = [];

    public function __construct(string $name, ?string $label = null)
    {
        $this->name = $name;
        $this->label = $label ?? Str::title($name);
    }
}
