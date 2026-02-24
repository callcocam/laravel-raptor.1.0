<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Callcocam\LaravelRaptor\Support\Form\Columns\Types;

use Callcocam\LaravelRaptor\Support\Form\Columns\Types\TextField;

class EmailField extends TextField
{
    public function __construct(string $name, ?string $label = null)
    {
        parent::__construct($name, $label);
        if ($this->rules === null) {
            $this->addRule('email');
        }
    }
}