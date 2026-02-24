<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Shared;

use Closure;

trait BelongsToValidation
{
    protected bool $required = false;

    protected array $messages = [];

    /**
     * Marca o campo como obrigatório
     */
    public function required(bool $required = true): static
    {
        $this->required = $required;

        // Adiciona 'required' às rules se não existir
        if ($required && ! $this->hasRule('required')) {
            $this->addRule('required');
        }

        return $this;
    }

    /**
     * Marca o campo como opcional (nullable)
     */
    public function nullable(bool $nullable = true): static
    {
        if (! $nullable || ! $this->hasRule('required')) {
            return $this;
        }

        $rules = $this->rulesToArray();
        $rules = array_values(array_filter($rules, function ($rule): bool {
            $r = (string) $rule;
            if ($r === 'required') {
                return false;
            }
            if (str_starts_with($r, 'required|')) {
                return false;
            }
            if (str_ends_with($r, '|required') || str_contains($r, '|required|')) {
                return false;
            }
            return true;
        }));
        $this->rules = $rules;

        return $this;
    }

    /**
     * Define as regras de validação do campo
     */
    public function rules(array|string|Closure $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Adiciona uma regra de validação
     */
    public function addRule(string $rule): static
    {
        if (is_string($this->rules)) {
            $this->rules .= '|'.$rule;
        } elseif (is_array($this->rules)) {
            $this->rules[] = $rule;
        } else {
            $this->rules = [$rule];
        }

        return $this;
    }

    /**
     * Define mensagens de validação customizadas
     */
    public function messages(array $messages): static
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Verifica se o campo é obrigatório
     */
    public function isRequired(): bool
    {
        return $this->required || $this->hasRule('required');
    }

    /**
     * Retorna as regras de validação
     */
    public function getRules($record = null): array|string
    {
        return $this->evaluate($this->rules, [
            'record' => $record,
            'attribute' => $this->getName(),
        ]);
    }

    /**
     * Retorna as mensagens de validação
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Verifica se uma regra específica existe
     */
    protected function hasRule(string $rule): bool
    {
        $rules = $this->rulesToArray();

        foreach ($rules as $r) {
            $r = (string) $r;
            if ($r === $rule) {
                return true;
            }
            if (str_starts_with($r, $rule.'|') || str_ends_with($r, '|'.$rule) || str_contains($r, '|'.$rule.'|')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna as regras normalizadas como array (para uso interno)
     *
     * @return array<int, string>
     */
    protected function rulesToArray(): array
    {
        $raw = $this->rules ?? [];
        $evaluated = $this->evaluate($raw, ['record' => null, 'attribute' => $this->getName()]);
        if (is_string($evaluated)) {
            return array_filter(array_map('trim', explode('|', $evaluated)));
        }

        return is_array($evaluated) ? array_values($evaluated) : [];
    }
}
