<template>
  <div v-if="selectedCount > 0" class="flex items-center gap-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-900 dark:bg-amber-950">
    <div class="flex-1">
      <p class="text-sm font-medium text-amber-900 dark:text-amber-100">
        {{ selectedCount }} {{ selectedCount === 1 ? 'item' : 'itens' }} selecionado{{ selectedCount === 1 ? '' : 's' }}
      </p>
    </div>
    <div class="flex items-center gap-2">
      <ActionRenderer 
        v-for="action in bulkActionsList" 
        :key="action.name" 
        :action="action" 
        :record="{ selectedIds: selectedIds }"
        :icon-only="false"
        @click="onAction(action)"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import ActionRenderer from '@raptor/components/actions/ActionRenderer.vue'
import { TableAction } from '@raptor/composables'
import { computed } from 'vue'

export interface BulkAction extends Omit<TableAction, 'url'> {
  name: string
  label: string
  type?: string
  icon?: string
  variant?: string
  disabled?: boolean
  actionName?: string
  title?: string
  description?: string
  confirmText?: string
  cancelText?: string
  confirmVariant?: string
  confirmIcon?: string
  requireTextConfirmation?: boolean
  confirmationText?: string
  confirmationPlaceholder?: string
  url?: string | undefined
  [key: string]: any
}

const props = defineProps<{
  selectedCount: number
  selectedIds: (string | number)[]
  bulkActions: BulkAction[] | Record<string, BulkAction>
}>()

const emit = defineEmits<{
  (e: 'action', action: BulkAction): void
}>()

const bulkActionsList = computed(() =>
  Array.isArray(props.bulkActions) ? props.bulkActions : Object.values(props.bulkActions ?? {})
)

function onAction(action: BulkAction) {
  emit('action', action)
}
</script>
