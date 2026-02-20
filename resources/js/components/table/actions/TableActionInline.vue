<!--
  TableActionInline - Exibe ações da linha em formato inline (botões lado a lado).
  Recebe :actions (array) e :record; emite @action ao clicar numa ação.
-->
<template>
  <div class="flex items-center gap-1">
    <ActionRenderer 
      v-for="action in actionsList" 
      :key="action.name" 
      :action="action" 
      :record="record"
      :icon-only="true"
      @click="onAction(action)"
    />
  </div>
</template>

<script lang="ts" setup>
import ActionRenderer from '@raptor/components/actions/ActionRenderer.vue';
import { computed } from 'vue'

const props = defineProps<{
  actions: Record<string, { name: string; label: string; url?: string }> | Array<{ name: string; label: string; url?: string }>
  record: Record<string, unknown>
}>()

const emit = defineEmits<{
  (e: 'action', action: { name: string; label: string; url?: string }): void
}>()

const actionsList = computed(() =>
  Array.isArray(props.actions) ? props.actions : Object.values(props.actions ?? {})
)

function onAction(act: { name: string; label: string; url?: string }) {
  emit('action', act)
}
</script>
