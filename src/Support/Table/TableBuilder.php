<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table;

use Callcocam\LaravelRaptor\Support\Concerns\EvaluatesClosures;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TableBuilder
{
    use EvaluatesClosures;
    use WithColumns;

    public function __construct(protected Request $request, protected ?Model $model = null) {}

    public function render(): array
    {
        return [
            // TODO: Implement the render method
        ];
    }
}
