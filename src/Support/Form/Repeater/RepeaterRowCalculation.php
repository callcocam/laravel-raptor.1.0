<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form\Repeater;

use Callcocam\LaravelRaptor\Support\Concerns\FactoryPattern;

/**
 * Cálculo por linha do repeater.
 * Fórmula usa nomes de campos e operadores (+, -, *, /). Ex.: "quantity * unitPrice - discount"
 * O frontend avalia a fórmula com os valores da linha e grava no targetField.
 */
class RepeaterRowCalculation
{
    use FactoryPattern;

    protected string $targetField = '';

    protected string $formula = '';

    public function __construct() {}

    public function targetField(string $field): static
    {
        $this->targetField = $field;

        return $this;
    }

    public function formula(string $expression): static
    {
        $this->formula = $expression;

        return $this;
    }

    public function getTargetField(): string
    {
        return $this->targetField;
    }

    public function getFormula(): string
    {
        return $this->formula;
    }

    public function toArray(): array
    {
        return [
            'targetField' => $this->targetField,
            'formula' => $this->formula,
        ];
    }
}
