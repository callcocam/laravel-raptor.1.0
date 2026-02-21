<template>
  <component
    v-if="resolvedComponent"
    :is="resolvedComponent"
    :field="field"
    :model-value="modelValue"
    @update:model-value="emit('update:modelValue', $event)"
  />
</template>

<script lang="ts" setup>
import { computed, type Component } from 'vue'
import ComponentRegistry from '@raptor/utils/ComponentRegistry'
import type { FormField } from '@raptor/types'

const props = defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const resolvedComponent = computed<Component | null>(() => {
  const name = props.field.component ?? 'form-field-text'
  return ComponentRegistry.get(name) ?? ComponentRegistry.get('form-field-text')
})
</script>
