<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Callcocam\LaravelRaptor\Support\Form\Columns\Types;

use Callcocam\LaravelRaptor\Support\Form\Columns\Types\TextField;
use Closure;

class PasswordField extends TextField
{
    protected Closure|string|null $component = 'form-field-password';

    public function __construct(string $name, ?string $label = null)
    {
        parent::__construct($name, $label);
        $this->inputType('password');
        if ($this->rules === null) {
            $this->addRule('string');
            $this->addRule('min:8');
        }
    }
} 