<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Sluggable;

class SlugOptions
{
    /** @var array|callable */
    public $generateSlugFrom;

    public ?string $slugField = null;

    public bool $generateUniqueSlugs = true;

    public bool $uniqueWithinTenant = false;

    public int $maximumLength = 250;

    public bool $generateSlugsOnCreate = true;

    public bool $generateSlugsOnUpdate = true;

    public string $slugSeparator = '-';

    public string $slugLanguage = 'en';

    public static function create(): self
    {
        return new static;
    }

    /**
     * @param  string|array|callable  $fieldName
     */
    public function generateSlugsFrom($fieldName): self
    {
        if (is_string($fieldName)) {
            $fieldName = [$fieldName];
        }

        $this->generateSlugFrom = $fieldName;

        return $this;
    }

    public function saveSlugsTo(string $fieldName): self
    {
        $this->slugField = $fieldName;

        return $this;
    }

    public function allowDuplicateSlugs(): self
    {
        $this->generateUniqueSlugs = false;

        return $this;
    }

    /**
     * Em multi-tenant, verifica unicidade apenas dentro do tenant atual
     * em vez de remover todos os global scopes.
     */
    public function uniqueWithinTenant(): self
    {
        $this->uniqueWithinTenant = true;

        return $this;
    }

    public function slugsShouldBeNoLongerThan(int $maximumLength): self
    {
        $this->maximumLength = $maximumLength;

        return $this;
    }

    public function doNotGenerateSlugsOnCreate(): self
    {
        $this->generateSlugsOnCreate = false;

        return $this;
    }

    public function doNotGenerateSlugsOnUpdate(): self
    {
        $this->generateSlugsOnUpdate = false;

        return $this;
    }

    public function usingSeparator(string $separator): self
    {
        $this->slugSeparator = $separator;

        return $this;
    }

    public function usingLanguage(string $language): self
    {
        $this->slugLanguage = $language;

        return $this;
    }
}
