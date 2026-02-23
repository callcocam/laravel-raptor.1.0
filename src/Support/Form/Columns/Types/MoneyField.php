<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Form\Columns\Types;

use Callcocam\LaravelRaptor\Support\Form\Columns\Column;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MoneyField extends Column
{
    protected Closure|string|null $component = 'form-field-money';

    protected string $currency = 'BRL';

    protected int $decimals = 2;

    protected ?string $decimalSeparator = null;

    protected ?string $thousandsSeparator = null;

    public function currency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function decimals(int $decimals): static
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function decimalSeparator(string $separator): static
    {
        $this->decimalSeparator = $separator;

        return $this;
    }

    public function thousandsSeparator(string $separator): static
    {
        $this->thousandsSeparator = $separator;

        return $this;
    }

    protected function getType(): string
    {
        return 'money';
    }

    public function toArray(?Model $model = null, ?Request $request = null): array
    {
        $arr = array_merge(parent::toArray($model, $request), [
            'currency' => $this->currency,
            'decimals' => $this->decimals,
        ]);
        if ($this->decimalSeparator !== null) {
            $arr['decimalSeparator'] = $this->decimalSeparator;
        }
        if ($this->thousandsSeparator !== null) {
            $arr['thousandsSeparator'] = $this->thousandsSeparator;
        }

        return $arr;
    }
}
