<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Sluggable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    protected ?SlugOptions $slugOptions = null;

    /**
     * @return string|bool
     */
    protected function slugTo(): string|bool
    {
        return config('raptor.sluggable.slug', 'slug');
    }

    /**
     * @return string|bool
     */
    protected function slugFrom(): string|bool
    {
        return config('raptor.sluggable.name', 'name');
    }

    /**
     * Default: gera SlugOptions a partir de slugFrom() -> slugTo().
     * Sobrescreva no model para customizar.
     */
    public function getSlugOptions(): ?SlugOptions
    {
        if (is_string($this->slugTo())) {
            return SlugOptions::create()
                ->generateSlugsFrom($this->slugFrom())
                ->saveSlugsTo($this->slugTo());
        }

        return null;
    }

    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            $model->generateSlugOnCreate();
        });

        static::updating(function (Model $model) {
            $model->generateSlugOnUpdate();
        });
    }

    protected function generateSlugOnCreate(): void
    {
        $this->slugOptions = $this->getSlugOptions();

        if (! $this->slugOptions) {
            return;
        }

        if (! $this->slugOptions->generateSlugsOnCreate) {
            return;
        }

        $this->addSlug();
    }

    protected function generateSlugOnUpdate(): void
    {
        $this->slugOptions = $this->getSlugOptions();

        if (! $this->slugOptions) {
            return;
        }

        if (! $this->slugOptions->generateSlugsOnUpdate) {
            return;
        }

        $this->addSlug();
    }

    public function generateSlug(): void
    {
        $this->slugOptions = $this->getSlugOptions();

        $this->addSlug();
    }

    protected function addSlug(): void
    {
        $this->ensureValidSlugOptions();

        $slug = $this->generateNonUniqueSlug();

        if ($this->slugOptions->generateUniqueSlugs) {
            $slug = $this->makeSlugUnique($slug);
        }

        $slugField = $this->slugOptions->slugField;

        $this->$slugField = $slug;
    }

    protected function generateNonUniqueSlug(): string
    {
        $slugField = $this->slugOptions->slugField;

        if ($this->hasCustomSlugBeenUsed() && ! empty($this->$slugField)) {
            return $this->$slugField;
        }

        return Str::slug($this->getSlugSourceString(), $this->slugOptions->slugSeparator, $this->slugOptions->slugLanguage);
    }

    protected function hasCustomSlugBeenUsed(): bool
    {
        $slugField = $this->slugOptions->slugField;

        return $this->getOriginal($slugField) != $this->$slugField;
    }

    protected function getSlugSourceString(): string
    {
        if (is_callable($this->slugOptions->generateSlugFrom)) {
            $slugSourceString = call_user_func($this->slugOptions->generateSlugFrom, $this);

            return $this->generateSubstring($slugSourceString);
        }

        $slugSourceString = collect($this->slugOptions->generateSlugFrom)
            ->map(fn (string $fieldName) => data_get($this, $fieldName, ''))
            ->implode($this->slugOptions->slugSeparator);

        return $this->generateSubstring($slugSourceString);
    }

    protected function makeSlugUnique(string $slug): string
    {
        $originalSlug = $slug;
        $i = 1;

        while ($this->otherRecordExistsWithSlug($slug) || $slug === '') {
            $slug = $originalSlug.$this->slugOptions->slugSeparator.$i++;
        }

        return $slug;
    }

    protected function otherRecordExistsWithSlug(string $slug): bool
    {
        $key = $this->getKey();

        if ($this->getIncrementing()) {
            if (! $key) {
                $key = '0';
            }
        }

        $query = static::where($this->slugOptions->slugField, $slug)
            ->where($this->getKeyName(), '!=', $key);

        if (! $this->slugOptions->uniqueWithinTenant) {
            $query->withoutGlobalScopes();
        }

        if ($this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        return $query->exists();
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array(
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            class_uses_recursive(static::class)
        );
    }

    protected function ensureValidSlugOptions(): void
    {
        if (is_array($this->slugOptions->generateSlugFrom) && ! count($this->slugOptions->generateSlugFrom)) {
            throw InvalidOption::missingFromField();
        }

        if (! strlen($this->slugOptions->slugField)) {
            throw InvalidOption::missingSlugField();
        }

        if ($this->slugOptions->maximumLength <= 0) {
            throw InvalidOption::invalidMaximumLength();
        }
    }

    protected function generateSubstring(string $slugSourceString): string
    {
        return mb_substr($slugSourceString, 0, $this->slugOptions->maximumLength);
    }

    /**
     * @param  Builder<Model>  $query
     */
    public function scopeWhereSlug(Builder $query, string $slug): Builder
    {
        $field = $this->getSlugOptions()?->slugField ?? $this->slugTo();

        return $query->where($field, $slug);
    }
}
