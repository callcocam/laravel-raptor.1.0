<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Callcocam\LaravelRaptor\Support\Form\Columns\Types;

use Callcocam\LaravelRaptor\Support\Form\Columns\Types\PasswordField;

class PasswordConfirmationField extends PasswordField
{
    public function __construct(string $name, ?string $label = null, protected string $passwordFieldName = 'password')
    {
        parent::__construct($name, $label);
        $this->rules(['required', 'string', 'min:8', 'same:'.$this->passwordFieldName]);
    }
}
