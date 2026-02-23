<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form\Repeater;

use Callcocam\LaravelRaptor\Support\Concerns\FactoryPattern;

/**
 * Cálculo vertical (coluna): agrega valores de um campo em todos os itens.
 * Ex.: somar "total" de cada linha e gravar no campo do form "items_total".
 */
class RepeaterSummaryCalculation
{
    use FactoryPattern;

    public const SUM = 'sum';

    public const AVG = 'avg';

    public const COUNT = 'count';

    public function __construct(
        protected string $sourceField = '',
        protected string $operation = self::SUM,
        protected string $targetField = '',
    ) {}

    /**
     * Campo do item que será agregado (ex.: "total").
     */
    public function sourceField(string $field): static
    {
        $this->sourceField = $field;

        return $this;
    }

    /**
     * Operação: sum, avg, count.
     */
    public function operation(string $op): static
    {
        $this->operation = $op;

        return $this;
    }

    public function sum(string $sourceField): static
    {
        $this->sourceField = $sourceField;
        $this->operation = self::SUM;

        return $this;
    }

    public function avg(string $sourceField): static
    {
        $this->sourceField = $sourceField;
        $this->operation = self::AVG;

        return $this;
    }

    public function count(string $sourceField = ''): static
    {
        $this->sourceField = $sourceField;
        $this->operation = self::COUNT;

        return $this;
    }

    /**
     * Nome do campo do formulário que receberá o resultado (ex.: "items_total").
     */
    public function targetField(string $field): static
    {
        $this->targetField = $field;

        return $this;
    }

    public function getSourceField(): string
    {
        return $this->sourceField;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getTargetField(): string
    {
        return $this->targetField;
    }

    public function toArray(): array
    {
        return [
            'sourceField' => $this->sourceField,
            'operation' => $this->operation,
            'targetField' => $this->targetField,
        ];
    }
}
