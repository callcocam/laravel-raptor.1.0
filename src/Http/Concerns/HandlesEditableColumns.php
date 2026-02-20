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

        // Se é uma coluna editável, executa callback de atualização
        if (method_exists($column, 'update') && method_exists($column, 'getValidationRules')) {
            // Validação
            $rules = $column->getValidationRules();
            if (! empty($rules)) {
                try {
                    $request->validate([
                        'value' => $rules,
                    ]);
                } catch (ValidationException $e) {
                    return redirect()->back()->with([
                        'message' => 'Validação falhou',
                        'errors' => $e->errors(),
                    ], 422);
                }
            }

            // Executa callback
            try {
                $result = $column->update($model, $value, $request);

                // Atualiza o modelo
                $model->{$fieldName} = $value;
                $model->save();

                return redirect()->back()->with([
                    'message' => 'Campo atualizado com sucesso',
                    'data' => $model,
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with([
                    'message' => 'Erro ao atualizar: ' . $e->getMessage(),
                ], 500);
            }
        }

        // Fallback: atualização simples
        $model->{$fieldName} = $value;
        $model->save();

        return redirect()->back()->with([
            'message' => 'Campo atualizado com sucesso',
            'data' => $model,
        ]);
    }
}
