<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Shared;

use Closure;

trait BelongsToImage
{
    protected ?int $imageWidth = null;

    protected ?int $imageHeight = null;

    protected bool $isRounded = false;

    protected Closure|string|null $fallbackImage = null;

    /**
     * Define largura da imagem (em pixels)
     */
    public function imageWidth(int $width): static
    {
        $this->imageWidth = $width;

        return $this;
    }

    /**
     * Define altura da imagem (em pixels)
     */
    public function imageHeight(int $height): static
    {
        $this->imageHeight = $height;

        return $this;
    }

    /**
     * Define tamanho da imagem (largura x altura)
     */
    public function imageSize(int $width, int $height): static
    {
        $this->imageWidth = $width;
        $this->imageHeight = $height;

        return $this;
    }

    /**
     * Torna a imagem com cantos arredondados
     */
    public function rounded(bool $rounded = true): static
    {
        $this->isRounded = $rounded;

        return $this;
    }

    /**
     * Define imagem de fallback quando a principal não existir
     */
    public function fallback(Closure|string|null $fallback): static
    {
        $this->fallbackImage = $fallback;

        return $this;
    }

    /**
     * Obtém largura da imagem
     */
    public function getImageWidth(): ?int
    {
        return $this->imageWidth;
    }

    /**
     * Obtém altura da imagem
     */
    public function getImageHeight(): ?int
    {
        return $this->imageHeight;
    }

    /**
     * Verifica se a imagem é arredondada
     */
    public function isRounded(): bool
    {
        return $this->isRounded;
    }

    /**
     * Obtém imagem de fallback
     */
    public function getFallback(): ?string
    {
        if ($this->fallbackImage === null) {
            return null;
        }

        return $this->evaluate($this->fallbackImage);
    }
}
