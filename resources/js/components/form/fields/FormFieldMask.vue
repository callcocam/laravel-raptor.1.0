<template>
  <Input
      :id="field.name"
      :data-slot="inInputGroup ? 'input-group-control' : undefined"
      :model-value="maskedValue"
      type="text"
      :placeholder="(field.placeholder as string) ?? (field.label as string) ?? undefined"
      :disabled="(field.disabled as boolean) ?? false"
      :readonly="(field.readonly as boolean) ?? false"
      :class="cn('w-full', inInputGroup && 'flex-1 rounded-none border-0 shadow-none focus-visible:ring-0 dark:bg-transparent')"
      :maxlength="maxLength"
      @input="handleInput"
  />
</template>

<script lang="ts" setup>
import { computed, inject } from 'vue'
import type { ComputedRef } from 'vue'
import { cn } from '@/lib/utils'
import { Input } from '@/components/ui/input'
import { useFormField } from '@raptor/composables/useFormField'
import type { FormField } from '@raptor/types'


const props = defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const inInputGroup = inject<ComputedRef<boolean>>('inInputGroup', { value: false } as ComputedRef<boolean>)

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
