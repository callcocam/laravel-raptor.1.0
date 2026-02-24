<?php

/**
 * Atalhos para as regras de validação mais comuns do Laravel.
 * Uso em conjunto com BelongsToValidation (ex.: $field->email()->required()).
 *
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Validation;

class ValidationRules
{
    public static function required(): string
    {
        return 'required';
    }

    public static function nullable(): string
    {
        return 'nullable';
    }

    public static function email(): string
    {
        return 'email';
    }

    public static function string(): string
    {
        return 'string';
    }

    public static function min(int $value): string
    {
        return 'min:'.$value;
    }

    public static function max(int $value): string
    {
        return 'max:'.$value;
    }

    public static function minLength(int $value): string
    {
        return 'min:'.$value;
    }

    public static function maxLength(int $value): string
    {
        return 'max:'.$value;
    }

    public static function between(int $min, int $max): string
    {
        return "between:{$min},{$max}";
    }

    public static function same(string $field): string
    {
        return 'same:'.$field;
    }

    public static function confirmed(): string
    {
        return 'confirmed';
    }

    public static function regex(string $pattern): string
    {
        return 'regex:'.$pattern;
    }

    /**
     * @param  array<int, string|int>  $values
     */
    public static function in(array $values): string
    {
        return 'in:'.implode(',', $values);
    }

    public static function numeric(): string
    {
        return 'numeric';
    }

    public static function integer(): string
    {
        return 'integer';
    }

    public static function url(): string
    {
        return 'url';
    }

    public static function alpha(): string
    {
        return 'alpha';
    }

    public static function alphaNum(): string
    {
        return 'alpha_num';
    }

    public static function date(): string
    {
        return 'date';
    }

    public static function dateFormat(string $format): string
    {
        return 'date_format:'.$format;
    }

    public static function boolean(): string
    {
        return 'boolean';
    }

    public static function array(): string
    {
        return 'array';
    }

    /**
     * Retorna a chave da regra para mensagens (ex.: "min:8" -> "min").
     */
    public static function ruleKey(string $rule): string
    {
        $parts = explode(':', $rule, 2);

        return trim($parts[0]);
    }
}
