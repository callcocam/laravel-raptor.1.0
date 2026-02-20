<!--
 * ActionRenderer - Renderiza ações dinamicamente
 *
 * Usa ComponentRegistry para obter o componente correto (action.component ou actionType).
 -->
<template>
  <component
    v-if="resolvedComponent"
    :is="resolvedComponent"
    :action="action"
    :record="record"
    :column="column"
    @click="handleClick"
  />
  <a
    v-else-if="action.url"
    :href="action.url"
    class="block px-3 py-1.5 text-sm hover:bg-accent"
    @click.prevent="handleClick"
  >
    {{ action.label }}
  </a>
  <span v-else class="px-3 py-1.5 text-sm text-muted-foreground">{{ action.label }}</span>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import ComponentRegistry from '@raptor/utils/ComponentRegistry'

export interface TableAction {
  name: string
  label: string
  component?: string
  actionType?: string
  url?: string
  confirm?: boolean
  to?: string
  target?: string
  [key: string]: unknown
}

const props = defineProps<{
  action: TableAction
  record?: Record<string, unknown>
  column?: Record<string, unknown>
}>() 
const emit = defineEmits<{
  (e: 'click', event: Event): void
}>()
/**
 * Obtém o componente a ser renderizado do ActionRegistry
 *
 * Lógica de seleção (prioridade decrescente):
 * 1. Se 'component' está especificado → usa ele
 * 2. Se 'actionType' está definido → usa mapeamento de tipo
 * 3. Auto-detecção baseada em propriedades (legado)
 * 4. Fallback para 'action-button'
 *
 * Mapeamento de actionType:
 * - 'link' → 'action-link' (navegação GET)
 * - 'callback' → 'action-callback' (executa função JS)
 * - 'modal' → 'action-modal-form' (modal com formulário)
 * - 'api' → 'action-confirm' (API call com confirmação)
 * - 'table' → 'action-modal-table' (futuro)
 */
const typeMap: Record<string, string> = {
  link: 'action-link',
  callback: 'action-callback',
  modal: 'action-modal-form',
  api: 'action-confirm',
  table: 'action-modal-table',
}

const resolvedComponent = computed(() => {
  let name = props.action.component
  if (name && ComponentRegistry.get(name)) return ComponentRegistry.get(name)
  if (props.action.actionType) {
    name = typeMap[props.action.actionType]
    if (name && ComponentRegistry.get(name)) return ComponentRegistry.get(name)
  }
  if (!name) {
    if (props.action.confirm && props.action.to) name = 'action-link-confirm'
    else if (props.action.confirm) name = 'action-confirm'
    else if (props.action.to || props.action.url) name = 'action-link'
    else if (props.action.target === 'modal') name = 'action-modal'
    else name = 'action-button'
    const c = ComponentRegistry.get(name)
    if (c) return c
  }
  return ComponentRegistry.get('action-button') ?? null
})

const handleClick = (event: Event) => {
  // Re-emite o evento de clique para o pai 
  emit('click', event)
}
</script>
