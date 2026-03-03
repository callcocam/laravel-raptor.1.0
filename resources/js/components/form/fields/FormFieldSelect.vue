<template>
  <Select
      :model-value="String(modelValue ?? '')"
      @update:model-value="emit('update:modelValue', $event)"
    >
      <SelectTrigger :id="field.name" class="w-full">
        <SelectValue :placeholder="(field.placeholder as string) ?? 'Selecione...'" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem
          v-for="opt in (field.options ?? [])"
          :key="String(opt.value)"
          :value="String(opt.value)"
        >
          {{ opt.label }}
        </SelectItem>
      </SelectContent>
    </Select>
</template>

<script lang="ts" setup>
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import type { FormField } from '@raptor/types'


defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()
</script>
