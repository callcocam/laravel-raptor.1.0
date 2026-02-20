<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types;

use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToDateTime;
use Callcocam\LaravelRaptor\Support\Table\Columns\Column;
use Closure;

class DateTimeColumn extends Column
{
    use BelongsToDateTime;

    protected Closure|string|null $component = 'datetime-table-column';

    public function render(mixed $value, $row = null): mixed
    {
        $formatted = $this->formatDateTime($value); 
        return $this->getFormattedValue($formatted ?? $value, $row);
    }


    public function getInputType(): string
    {
        return 'datetime-local';
    }

    public function showsDate(): static
    {
        $this->format('Y-m-d');

        return $this;
    }

    public function showsTime(): static
    {
        $this->format('H:i:s');

        return $this;
    }

    public function showsDateTime(): static
    {
        $this->format('Y-m-d H:i:s');

        return $this;
    }

    // Formata humano 
    public function formatHumanReadable(): static
    {
        $this->format('d/m/Y H:i:s');

        return $this;
    }

    // Formato ISO 8601
    public function formatISO8601(): static
    {
        $this->format('Y-m-d\TH:i:s');

        return $this;
    }

    // Data no formato brasileiro (dd/mm/yyyy)
    public function formatDateBrazilian(): static
    {
        $this->format('d/m/Y');

        return $this;
    }

    // Data no formato americano (mm/dd/yyyy)
    public function formatDateAmerican(): static
    {
        $this->format('m/d/Y');

        return $this;
    }

    // Data no formato europeu (dd.mm.yyyy)
    public function formatDateEuropean(): static
    {
        $this->format('d.m.Y');

        return $this;
    }

    // Data longa em português (31 de janeiro de 2025)
    public function formatDateLongPT(): static
    {
        $this->format('d \d\e F \d\e Y');

        return $this;
    }

    // Data longa em inglês (January 31, 2025)
    public function formatDateLongEN(): static
    {
        $this->format('F d, Y');

        return $this;
    }

    // Hora 24h (HH:mm)
    public function formatTime24h(): static
    {
        $this->format('H:i');

        return $this;
    }

    // Hora 24h com segundos (HH:mm:ss)
    public function formatTime24hWithSeconds(): static
    {
        $this->format('H:i:s');

        return $this;
    }

    // Data e hora brasileira (dd/mm/yyyy HH:mm)
    public function formatDateTimeShortBrazilian(): static
    {
        $this->format('d/m/Y H:i');

        return $this;
    }

    // Data e hora brasileira completa (dd/mm/yyyy HH:mm:ss)
    public function formatDateTimeFullBrazilian(): static
    {
        $this->format('d/m/Y H:i:s');

        return $this;
    }

    // Data e hora americana (mm/dd/yyyy HH:mm)
    public function formatDateTimeShortAmerican(): static
    {
        $this->format('m/d/Y H:i');

        return $this;
    }

    // Data e hora europeia (dd.mm.yyyy HH:mm:ss)
    public function formatDateTimeEuropean(): static
    {
        $this->format('d.m.Y H:i:s');

        return $this;
    }

    // Formato unix timestamp
    public function formatUnixTimestamp(): static
    {
        $this->format('U');

        return $this;
    }

    // Apenas ano
    public function formatYear(): static
    {
        $this->format('Y');

        return $this;
    }

    // Mês e ano (01/2025)
    public function formatMonthYear(): static
    {
        $this->format('m/Y');

        return $this;
    }

    // Dia da semana (Monday, Tuesday, etc)
    public function formatDayOfWeek(): static
    {
        $this->format('l');

        return $this;
    }

    // Dia da semana abreviado (Mon, Tue, etc)
    public function formatDayOfWeekShort(): static
    {
        $this->format('D');

        return $this;
    }

    // Formato relativo em português (há 2 horas, ontem, etc)
    public function formatRelativePT(): static
    {
        // Este é um formato especial que será tratado em outro lugar
        $this->format('relative-pt');

        return $this;
    }

    // Formato relativo em inglês (2 hours ago, yesterday, etc)
    public function formatRelativeEN(): static
    {
        // Este é um formato especial que será tratado em outro lugar
        $this->format('relative-en');

        return $this;
    }
 

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'format' => $this->getFormat(),
            'timezone' => $this->getTimezone(),
            'showDate' => $this->showsDate(),
            'showTime' => $this->showsTime(),
        ]);
    }
}
