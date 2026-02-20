<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Shared;

use Closure;
use DateTime;

trait BelongsToDateTime
{
    protected Closure|string|null $format = 'd/m/Y H:i:s';

    protected ?string $timezone = null;

    protected bool $showTime = true;

    protected bool $showDate = true;

    /**
     * Define formato da data/hora (usa formato PHP)
     * Ex: 'd/m/Y', 'H:i:s', 'd/m/Y H:i'
     */
    public function format(Closure|string $format): static
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Define fuso horário
     */
    public function timezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Exibe apenas hora (sem data)
     */
    public function timeOnly(): static
    {
        $this->showTime = true;
        $this->showDate = false;

        return $this;
    }

    /**
     * Exibe apenas data (sem hora)
     */
    public function dateOnly(): static
    {
        $this->showDate = true;
        $this->showTime = false;

        return $this;
    }

    /**
     * Exibe data e hora
     */
    public function dateTime(): static
    {
        $this->showDate = true;
        $this->showTime = true;

        return $this;
    }

    /**
     * Obtém formato da data/hora
     */
    public function getFormat(): string
    {
        return (string) $this->evaluate($this->format);
    }

    /**
     * Obtém fuso horário
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * Verifica se exibe hora
     */
    public function showsTime(): bool
    {
        return $this->showTime;
    }

    /**
     * Verifica se exibe data
     */
    public function showsDate(): bool
    {
        return $this->showDate;
    }

    /**
     * Formata a data/hora
     */
    public function formatDateTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_string($value)) {
                $date = new DateTime($value);
            } elseif ($value instanceof DateTime) {
                $date = $value;
            } else {
                return $value->format($this->getFormat());
            }

            if ($this->timezone !== null) {
                $date->setTimezone(new \DateTimeZone($this->timezone));
            }

            return $date->format($this->getFormat());
        } catch (\Exception $e) {
            return null;
        }
    }
}
