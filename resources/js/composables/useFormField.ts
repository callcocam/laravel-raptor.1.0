import type { FormFieldEmitContract } from '@raptor/types'

/**
 * Padrão de emit para campos de formulário.
 * Todos os campos (incluindo customizados) devem emitir apenas 'update:modelValue'.
 * Use este composable para manter o contrato sem depender do nome do evento.
 */
export function useFormField(
  emit: (e: keyof FormFieldEmitContract, value: unknown) => void,
) {
  function emitChange(value: unknown) {
    emit('update:modelValue', value)
  }

  return { emitChange }
}
