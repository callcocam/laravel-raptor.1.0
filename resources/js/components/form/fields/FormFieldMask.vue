<template>
  <div class="space-y-2">
    <Label
      v-if="!labelRenderedByWrapper && field.label"
      :for="field.name"
    >
      {{ field.label }}
    </Label>
    <Input
      :id="field.name"
      :model-value="maskedValue"
      type="text"
      :placeholder="(field.placeholder as string) ?? (field.label as string) ?? undefined"
      :disabled="(field.disabled as boolean) ?? false"
      :readonly="(field.readonly as boolean) ?? false"
      class="w-full"
      :maxlength="maxLength"
      @input="handleInput"
    />
  </div>
</template>

<script lang="ts" setup>
import { computed, inject } from 'vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useFormField } from '@raptor/composables/useFormField'
import type { FormField } from '@raptor/types'

const labelRenderedByWrapper = inject('fieldLabelRenderedByWrapper', false)

const props = defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const { emitChange } = useFormField(emit)

const defaultTokens: Record<string, RegExp> = {
  '#': /\d/,
  A: /[a-zA-Z]/,
  N: /[a-zA-Z0-9]/,
  X: /./,
}

const maxLength = computed(() => {
  const mask = props.field.mask as string | undefined
  return mask ? mask.length : undefined
})

function applyMask(value: string): string {
  const mask = props.field.mask as string | undefined
  if (!mask) return value
  const tokens = {
    ...defaultTokens,
    ...(props.field.maskTokens as Record<string, RegExp> | undefined),
  }
  const cleanValue = value.replace(/[^\w]/g, '')
  let masked = ''
  let valueIndex = 0
  for (let i = 0; i < mask.length && valueIndex < cleanValue.length; i++) {
    const maskChar = mask[i]
    const token = tokens[maskChar]
    if (token) {
      const char = cleanValue[valueIndex]
      if (token.test(char)) {
        masked += char
        valueIndex++
      } else {
        break
      }
    } else {
      masked += maskChar
    }
  }
  return masked
}

const maskedValue = computed(() => {
  const v = props.modelValue
  if (v === null || v === undefined) return ''
  return applyMask(String(v))
})

function handleInput(event: Event): void {
  const input = event.target as HTMLInputElement
  const masked = applyMask(input.value)
  const unmasked = masked.replace(/[^\w]/g, '')
  emitChange(unmasked || null)
}
</script>
