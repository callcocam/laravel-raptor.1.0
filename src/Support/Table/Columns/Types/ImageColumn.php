<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types;

use Closure;
use Callcocam\LaravelRaptor\Support\Concerns\Shared\BelongsToImage;
use Callcocam\LaravelRaptor\Support\Table\Columns\Column;

class ImageColumn extends Column
{
    use BelongsToImage;

    protected Closure|string|null $component = 'image-table-column';

    public function render(mixed $value, $row = null): mixed
    {
        return $this->getFormattedValue($value, $row);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'imageWidth' => $this->getImageWidth(),
            'imageHeight' => $this->getImageHeight(),
            'isRounded' => $this->isRounded(),
            'fallback' => $this->getFallback(),
        ]);
    }
}
