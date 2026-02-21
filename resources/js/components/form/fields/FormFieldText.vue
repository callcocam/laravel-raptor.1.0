<template>
  <div class="space-y-2">
    <Label
      v-if="field.label"
      :for="field.name"
    >
      {{ field.label }}
    </Label>
    <Input
      :id="field.name"
      :model-value="modelValue ?? ''"
      :type="(field.inputType as string) ?? 'text'"
      :placeholder="(field.placeholder as string) ?? undefined"
      class="w-full"
      @update:model-value="emit('update:modelValue', $event)"
    />
  </div>
</template>

<script lang="ts" setup>
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { FormField } from '@raptor/types'

defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()
</script>
