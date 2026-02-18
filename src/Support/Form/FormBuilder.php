<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Callcocam\LaravelRaptor\Support\Form;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Callcocam\LaravelRaptor\Support\Concerns\Interacts\WithColumns;
use Callcocam\LaravelRaptor\Support\Concerns\EvaluatesClosures;

class FormBuilder
{   
    use WithColumns;
    use EvaluatesClosures;
    
    public function __construct(protected Request $request, protected ?Model $model = null)
    {
    }

    public function render(): array
    {
        return [
           // TODO: Implement the render method
        ];
    }
}