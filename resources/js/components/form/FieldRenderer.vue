<template>
  <FieldAddonsWrapper :field="field">
    <component
      v-if="resolvedComponent"
      :is="resolvedComponent"
      :field="field"
      :model-value="modelValue"
      @update:model-value="emit('update:modelValue', $event)"
    />
  </FieldAddonsWrapper>
</template>

<script lang="ts" setup>
import { computed, type Component } from 'vue'
import ComponentRegistry from '@raptor/utils/ComponentRegistry'
import FieldAddonsWrapper from '@raptor/components/form/FieldAddonsWrapper.vue'
import type { FormFieldOrSection } from '@raptor/types'

const props = defineProps<{
  field: FormFieldOrSection
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const resolvedComponent = computed<Component | null>(() => {
  const f = props.field as { type?: string; component?: string }
   
  const name = f.component ?? 'form-field-text'
  return (
    ComponentRegistry.get(name) ??
    ComponentRegistry.get('form-field-text') ??
    null
  )
})
</script>
