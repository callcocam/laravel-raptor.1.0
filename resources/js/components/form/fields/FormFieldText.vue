<template>
  <div class="space-y-2">
    <Input
      :id="field.name"
      :model-value="typeof modelValue === 'string' || typeof modelValue === 'number' ? modelValue : ''"
      :type="(field.inputType as string) ?? 'text'"
      :placeholder="(field.placeholder as string) ?? undefined"
      class="w-full"
      @update:model-value="emit('update:modelValue', $event)"
    />
  </div>
</template>

<script lang="ts" setup>
import { Input } from '@/components/ui/input'
import type { FormField } from '@raptor/types'

defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()
</script>
