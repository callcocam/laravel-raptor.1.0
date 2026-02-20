<!--
  TableActionInline - Dropdown com as ações da linha (view, edit, delete, etc.).
  Recebe :actions (array) e :record; emite @action ao clicar numa ação.
-->
<template>
  <div class="relative">
    <ActionRenderer v-for="action in actionsList" :key="action.name" :action="action" :record="record" />
  </div>
</template>

<script lang="ts" setup>
import ActionRenderer from '@raptor/components/actions/ActionRenderer.vue';
import { ref, computed } from 'vue'

const props = defineProps<{
  actions: Record<string, { name: string; label: string; url?: string }> | Array<{ name: string; label: string; url?: string }>
  record: Record<string, unknown>
}>()

const emit = defineEmits<{
  (e: 'action', action: { name: string; label: string; url?: string }): void
}>()

const open = ref(false)

const actionsList = computed(() =>
  Array.isArray(props.actions) ? props.actions : Object.values(props.actions ?? {})
)

function onAction(act: { name: string; label: string; url?: string }) {
  open.value = false
  emit('action', act)
}
</script>
