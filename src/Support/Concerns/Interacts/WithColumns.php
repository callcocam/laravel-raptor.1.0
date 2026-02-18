<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Support\Concerns\Interacts;

use Callcocam\LaravelRaptor\Support\AbstractColumn;  
use Closure;
use Illuminate\Database\Eloquent\Model;

trait WithColumns
{
    /**
     * Set the columns for the builder.
     * @param Closure|array $columns
     * @return static
     */
    public function columns(Closure|array $columns): static
    {
         $this->columns = $columns;
         return $this;
    }

    /**
     * Add a column to the builder.
     * @param Closure|AbstractColumn $column
     * @return static
     */
    public function addColumn(Closure|AbstractColumn $column): static
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * Get a column from the builder.
     * @param string $name
     * @return AbstractColumn
     */
    public function getColumn(string $name): AbstractColumn
    {
        return $this->columns[$name];
    }

    /**
     * Check if a column exists in the builder.
     * @param string $name
     * @return bool
     */
    public function hasColumn(string $name): bool
    {
        return isset($this->columns[$name]);
    }

    /**
     * Remove a column from the builder.
     * @param string $name
     * @return static
     */
    public function removeColumn(string $name): static
    {
        unset($this->columns[$name]);
        return $this;
    }

    /**
     * Get the columns for the builder.
     * @param Model|null $model
     * @return mixed
     */
    public function getColumns(?Model $model = null): mixed
    {
        return $this->evaluate($this->columns, [
            'model' => $model,
        ]);
    }
}