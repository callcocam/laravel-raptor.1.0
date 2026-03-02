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
  if (f.type === 'section') {
    return (
      ComponentRegistry.get(f.component ?? 'form-field-section') ??
      ComponentRegistry.get('form-field-section') ??
      null
    )
  }
  if (f.type === 'repeater') {
    return (
      ComponentRegistry.get(f.component ?? 'form-field-repeater') ??
      ComponentRegistry.get('form-field-repeater') ??
      null
    )
  }
  if (f.type === 'money') {
    return (
      ComponentRegistry.get(f.component ?? 'form-field-money') ??
      ComponentRegistry.get('form-field-money') ??
      null
    )
  }
  if (f.type === 'mask') {
    return (
      ComponentRegistry.get(f.component ?? 'form-field-mask') ??
      ComponentRegistry.get('form-field-mask') ??
      null
    )
  }
  if (f.type === 'busca-cep') {
    return (
      ComponentRegistry.get(f.component ?? 'form-field-busca-cep') ??
      ComponentRegistry.get('form-field-busca-cep') ??
      null
    )
  }
  if (f.type === 'password') {
    return (
      ComponentRegistry.get(f.component ?? 'form-field-password') ??
      ComponentRegistry.get('form-field-password') ??
      null
    )
  }
  if (f.component === 'form-field-combobox') {
    return (
      ComponentRegistry.get('form-field-combobox') ??
      null
    )
  }
  const name = f.component ?? 'form-field-text'
  return (
    ComponentRegistry.get(name) ??
    ComponentRegistry.get('form-field-text') ??
    null
  )
})
</script>
