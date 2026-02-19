<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Summarizers;

use Callcocam\LaravelRaptor\Support\Concerns\EvaluatesClosures;
use Callcocam\LaravelRaptor\Support\Concerns\FactoryPattern;
use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Sumarização de colunas da tabela.
 * Calcula aggregates (sum, avg, min, max, count) tanto da página quanto do total.
 */
class Summarizer
{
    use EvaluatesClosures;
    use FactoryPattern;

    protected string $column;

    protected string $function;

    protected Closure|string|null $label = null;

    protected ?Closure $formatUsing = null;

    public function __construct(string $column, string $function)
    {
        $this->column = $column;
        $this->function = $function;
    }

    public static function sum(string $column): static
    {
        return new static($column, 'sum');
    }

    public static function avg(string $column): static
    {
        return new static($column, 'avg');
    }

    public static function min(string $column): static
    {
        return new static($column, 'min');
    }

    public static function max(string $column): static
    {
        return new static($column, 'max');
    }

    public static function count(string $column = '*'): static
    {
        return new static($column, 'count');
    }

    public function label(Closure|string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function formatUsing(Closure $callback): static
    {
        $this->formatUsing = $callback;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getFunction(): string
    {
        return $this->function;
    }

    public function getLabel(): ?string
    {
        if ($this->label instanceof Closure) {
            return ($this->label)();
        }

        return $this->label;
    }

    /**
     * Calcula o aggregate via query (para o total geral).
     */
    public function computeFromQuery(Builder $query): mixed
    {
        $value = $query->{$this->function}($this->column);

        return $this->formatValue($value);
    }

    /**
     * Calcula o aggregate a partir de items já carregados (para a página).
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function computeFromRows(array $rows): mixed
    {
        $values = array_filter(
            array_map(fn ($row) => $row[$this->column] ?? null, $rows),
            fn ($v) => $v !== null && $v !== ''
        );

        if (count($values) === 0) {
            return $this->formatValue(0);
        }

        $result = match ($this->function) {
            'sum' => array_sum($values),
            'avg' => count($values) > 0 ? array_sum($values) / count($values) : 0,
            'min' => min($values),
            'max' => max($values),
            'count' => count($values),
        };

        return $this->formatValue($result);
    }

    /**
     * Serialização para o payload.
     */
    public function toArray(): array
    {
        return [
            'column' => $this->column,
            'function' => $this->function,
            'label' => $this->getLabel(),
        ];
    }

    protected function formatValue(mixed $value): mixed
    {
        if ($this->formatUsing !== null) {
            return ($this->formatUsing)($value);
        }

        return $value;
    }
}
