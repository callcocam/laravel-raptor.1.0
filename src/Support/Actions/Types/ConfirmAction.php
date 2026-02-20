<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Actions\Types;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Action com confirmação antes de executar.
 * O frontend exibe um Dialog de confirmação com título, descrição e botões customizáveis.
 */
class ConfirmAction extends CallbackAction
{
    protected Closure|string|null $component = 'confirm-action';

    protected Closure|string|null $title = null;

    protected Closure|string|null $description = null;

    protected string $confirmText = 'Confirmar';

    protected string $cancelText = 'Cancelar';

    protected string $confirmVariant = 'default';

    protected ?string $confirmIcon = null;

    protected bool $requireTextConfirmation = false;

    protected Closure|string|null $confirmationText = null;

    protected ?string $confirmationPlaceholder = null;

    protected bool $useRandomWord = false;

    /**
     * Lista padrão de palavras seguras para confirmação.
     * Palavras curtas, simples e sem conotação negativa.
     */
    protected array $confirmationWords = [
        'CONFIRMAR',
        'EXCLUIR',
        'DELETAR',
        'REMOVER',
        'APAGAR',
        'SIM',
        'CONTINUAR',
        'PROSSEGUIR',
        'AUTORIZAR',
        'ACEITAR',
    ];

    public function title(Closure|string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function description(Closure|string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function confirmText(string $text): static
    {
        $this->confirmText = $text;

        return $this;
    }

    public function cancelText(string $text): static
    {
        $this->cancelText = $text;

        return $this;
    }

    public function confirmVariant(string $variant): static
    {
        $this->confirmVariant = $variant;

        return $this;
    }

    public function confirmIcon(?string $icon): static
    {
        $this->confirmIcon = $icon;

        return $this;
    }

    /**
     * Habilita confirmação por texto digitado.
     * O usuário precisará digitar o texto especificado para confirmar a ação.
     */
    public function requireTextConfirmation(Closure|string|null $text = null, ?string $placeholder = null): static
    {
        $this->requireTextConfirmation = true;
        $this->confirmationText = $text;
        $this->confirmationPlaceholder = $placeholder;

        return $this;
    }

    /**
     * Define o texto que deve ser digitado para confirmar.
     */
    public function confirmationText(Closure|string $text): static
    {
        $this->confirmationText = $text;

        return $this;
    }

    /**
     * Define o placeholder do input de confirmação.
     */
    public function confirmationPlaceholder(string $placeholder): static
    {
        $this->confirmationPlaceholder = $placeholder;

        return $this;
    }

    /**
     * Habilita confirmação usando uma palavra aleatória da lista.
     * Opcionalmente pode passar uma lista customizada de palavras.
     */
    public function requireRandomTextConfirmation(?array $words = null, ?string $placeholder = null): static
    {
        $this->requireTextConfirmation = true;
        $this->useRandomWord = true;

        if ($words !== null) {
            $this->confirmationWords = $words;
        }

        if ($placeholder !== null) {
            $this->confirmationPlaceholder = $placeholder;
        }

        return $this;
    }

    /**
     * Define a lista de palavras para sorteio.
     */
    public function confirmationWords(array $words): static
    {
        $this->confirmationWords = $words;

        return $this;
    }

    /**
     * Sorteia uma palavra aleatória da lista.
     */
    protected function getRandomWord(): string
    {
        return $this->confirmationWords[array_rand($this->confirmationWords)];
    }

    public function render(?Model $model = null, ?Request $request = null): ?array
    {
        $base = parent::render($model, $request);
        if ($base === null) {
            return null;
        }

        $title = $this->evaluate($this->title, [
            'model' => $model,
            'request' => $request,
        ]);

        $description = $this->evaluate($this->description, [
            'model' => $model,
            'request' => $request,
        ]);

        $confirmationText = null;

        if ($this->useRandomWord) {
            $confirmationText = $this->getRandomWord();
        } elseif ($this->confirmationText instanceof Closure && $model !== null) {
            $confirmationText = ($this->confirmationText)($model);
        } elseif (is_string($this->confirmationText)) {
            $confirmationText = $this->confirmationText;
        }

        return array_merge($base, [
            'title' => $title,
            'description' => $description,
            'confirmText' => $this->confirmText,
            'cancelText' => $this->cancelText,
            'confirmVariant' => $this->confirmVariant,
            'confirmIcon' => $this->confirmIcon,
            'requireTextConfirmation' => $this->requireTextConfirmation,
            'confirmationText' => $confirmationText,
            'confirmationPlaceholder' => $this->confirmationPlaceholder ?? 'Digite para confirmar',
        ]);
    }
}
