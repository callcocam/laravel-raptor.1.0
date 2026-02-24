<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Shared;

use Callcocam\LaravelRaptor\Support\Validation\ValidationRules;
use Closure;

trait BelongsToValidation
{
    protected bool $required = false;

    /** @var array<string, string> Mensagens customizadas por chave (ex.: attribute.rule) */
    protected array $messages = [];

    /** @var array<string, string> Mensagens por regra (rule key => message), mescladas em getMessages() */
    protected array $ruleMessages = [];

    /**
     * Marca o campo como obrigatório
     */
    public function required(bool $required = true, ?string $message = null): static
    {
        $this->required = $required;

        if ($required && ! $this->hasRule('required')) {
            $this->addRule(ValidationRules::required(), $message);
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
     * Define as regras de validação do campo (substitui as atuais)
     */
    public function rules(array|string|Closure $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Adiciona uma regra de validação com mensagem opcional (ex.: addRule('email', 'E-mail inválido'))
     */
    public function addRule(string $rule, ?string $message = null): static
    {
        if (is_string($this->rules)) {
            $this->rules .= '|'.$rule;
        } elseif (is_array($this->rules)) {
            $this->rules[] = $rule;
        } else {
            $this->rules = [$rule];
        }
        if ($message !== null) {
            $this->ruleMessages[ValidationRules::ruleKey($rule)] = $message;
        }

        return $this;
    }

    /**
     * Define mensagens de validação customizadas (chaves no formato attribute.rule)
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
     * Retorna as mensagens de validação (prontas para o validador do Laravel)
     *
     * @return array<string, string>
     */
    public function getMessages(): array
    {
        $attr = $this->getName();
        $built = [];
        foreach ($this->ruleMessages as $ruleKey => $msg) {
            $built[$attr.'.'.$ruleKey] = $msg;
        }

        return array_merge($built, $this->messages);
    }

    // --- Atalhos (regras mais comuns) ---

    public function email(?string $message = null): static
    {
        return $this->addRule(ValidationRules::email(), $message);
    }

    public function string(?string $message = null): static
    {
        return $this->addRule(ValidationRules::string(), $message);
    }

    public function min(int $value, ?string $message = null): static
    {
        return $this->addRule(ValidationRules::min($value), $message);
    }

    public function max(int $value, ?string $message = null): static
    {
        return $this->addRule(ValidationRules::max($value), $message);
    }

    public function minLength(int $value, ?string $message = null): static
    {
        return $this->addRule(ValidationRules::minLength($value), $message);
    }

    public function maxLength(int $value, ?string $message = null): static
    {
        return $this->addRule(ValidationRules::maxLength($value), $message);
    }

    public function between(int $min, int $max, ?string $message = null): static
    {
        return $this->addRule(ValidationRules::between($min, $max), $message);
    }

    public function same(string $field, ?string $message = null): static
    {
        return $this->addRule(ValidationRules::same($field), $message);
    }

    public function confirmed(?string $message = null): static
    {
        return $this->addRule(ValidationRules::confirmed(), $message);
    }

    public function regex(string $pattern, ?string $message = null): static
    {
        return $this->addRule(ValidationRules::regex($pattern), $message);
    }

    /**
     * @param  array<int, string|int>  $values
     */
    public function in(array $values, ?string $message = null): static
    {
        return $this->addRule(ValidationRules::in($values), $message);
    }

    public function numeric(?string $message = null): static
    {
        return $this->addRule(ValidationRules::numeric(), $message);
    }

    public function integer(?string $message = null): static
    {
        return $this->addRule(ValidationRules::integer(), $message);
    }

    public function url(?string $message = null): static
    {
        return $this->addRule(ValidationRules::url(), $message);
    }

    public function alpha(?string $message = null): static
    {
        return $this->addRule(ValidationRules::alpha(), $message);
    }

    public function alphaNum(?string $message = null): static
    {
        return $this->addRule(ValidationRules::alphaNum(), $message);
    }

    public function date(?string $message = null): static
    {
        return $this->addRule(ValidationRules::date(), $message);
    }

    public function dateFormat(string $format, ?string $message = null): static
    {
        return $this->addRule(ValidationRules::dateFormat($format), $message);
    }

    public function boolean(?string $message = null): static
    {
        return $this->addRule(ValidationRules::boolean(), $message);
    }

    public function array(?string $message = null): static
    {
        return $this->addRule(ValidationRules::array(), $message);
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
