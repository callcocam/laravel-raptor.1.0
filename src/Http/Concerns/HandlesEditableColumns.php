<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptor\Http\Concerns;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait HandlesEditableColumns
{
    /**
     * Atualiza um campo editável inline na tabela.
     * Rota: POST {resource}/{id}/update-field
     */
    public function updateField(Request $request, string $id)
    {
        $model = $this->getModel()->findOrFail($id);
        $this->authorizeRoute('update', $model);

        $fieldName = $request->input('field');
        $value = $request->input('value');

        if (! $fieldName) {
            abort(400, 'Campo não especificado.');
        }

        // Busca a coluna para validação
        $tableBuilder = $this->table($this->getTableBuilder($request));
        $column = collect($tableBuilder->getColumns())
            ->first(fn($col) => $col->getName() === $fieldName);

        if (! $column) {
            abort(404, "Coluna [{$fieldName}] não encontrada.");
        }

        // Validação
        $rules = $column->getValidationRules();
        if (! empty($rules)) {
            try {
                $request->validate([
                    'value' => $rules,
                ]);
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors());
            }
        }

        // Executa callback
        try {
            $result = $column->update($model, $value, $request); 
            // Atualiza o modelo
            $data[$fieldName] = data_get($result, 'value', $value);
            dd($data);
            $model->update($data);

            return $this->handleActionResult($result);
        } catch (\Exception $e) {
            return back()->withErrors(['value' => 'Erro ao atualizar: ' . $e->getMessage()]);
        }

        return $this->handleActionResult([
            'notification' => [
                'type' => 'error',
                'message' => __('Não foi possivel completar a atualização!!!')
            ]
        ]);
    }
}
