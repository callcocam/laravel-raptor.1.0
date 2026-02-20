<!--
  TableActionRenderer - Renderiza o bloco de ações da linha dinamicamente.

  Recebe o nome do componente (ex: 'table-action-inline') e o record (linha com .actions).
  Resolve o componente pelo nome no ComponentRegistry e repassa actions + record.
-->
<template>
  <component 
    :is="resolvedComponent"
    :actions="actionsList"
    :record="record"
    @action="onAction"
  />  
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import ComponentRegistry from '@raptor/utils/ComponentRegistry'

const props = defineProps<{
  component: string
  record: Record<string, unknown> & { actions?: Record<string, { name: string; label: string; url?: string }> }
}>() 
const emit = defineEmits<{
  (e: 'action', action: { name: string; label: string; url?: string }): void
}>()

const resolvedComponent = computed(() =>
  ComponentRegistry.get(props.component) ?? null
)

const actionsList = computed(() => {
  const a = props.record.actions
  return a ? Object.values(a) : []
})

function onAction(act: { name: string; label: string; url?: string }) {
  emit('action', act)
}
</script>
