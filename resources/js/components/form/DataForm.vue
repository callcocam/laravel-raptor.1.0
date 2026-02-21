<template>
  <div
    v-if="form"
    class="flex flex-1 flex-col gap-6"
  >
    <div
      v-if="form.headerActions?.length"
      class="flex items-center justify-end gap-2"
    >
      <FormHeaderActions :actions="form.headerActions" />
    </div>
    <component
      v-if="resolvedComponents.renderer"
      :is="resolvedComponents.renderer"
      :fields="form.fields"
      :values="form.values"
      :submit-url="form.submitUrl ?? null"
      :submit-method="form.submitMethod ?? 'post'"
      :footer-actions="form.footerActions ?? []"
      :grid-layout="form.gridLayout"
    />
  </div>
</template>

<script lang="ts" setup>
import { computed, type Component } from 'vue'
import ComponentRegistry from '@raptor/utils/ComponentRegistry'
import FormHeaderActions from '@raptor/components/form/FormHeaderActions.vue'
import type { FormPayload } from '@raptor/types'

const props = defineProps<{
  form: FormPayload | null | undefined
}>()

const resolvedComponents = computed(() => {
  const defaults: Record<string, string> = {
    renderer: 'form-renderer',
  }
  const components = props.form?.components ?? {}
  const resolved: Record<string, Component | null> = {}
  for (const [key, defaultName] of Object.entries(defaults)) {
    const name = components[key] ?? defaultName
    resolved[key] = ComponentRegistry.get(name) ?? null
  }
  return resolved as { renderer: Component | null }
})
</script>
