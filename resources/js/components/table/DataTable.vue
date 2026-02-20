<template>
  <div class="flex flex-1 flex-col gap-6">
    <component
      :is="resolvedComponents.header"
      :title="title"
      :subtitle="subtitle"
      :header-actions="headerActions"
      @action="handleHeaderAction"
    />
    <div class="space-y-4">
      <component
        :is="resolvedComponents.filters"
        v-model:search="search"
        search-placeholder="Filtrar..."
        :has-active-filters="hasActiveFilters"
        @reset="handleFiltersReset"
      />
      <component
        v-if="bulkActions && bulkActions.length > 0 && selectedIds.length > 0"
        :is="resolvedComponents.bulkActions"
        :selected-count="selectedIds.length"
        :selected-ids="selectedIds"
        :bulk-actions="bulkActions"
        @action="handleBulkAction"
      />
      <component
        :is="resolvedComponents.renderer"
        v-if="columns && data"
        :columns="columns"
        :data="data"
        :selectable="selectable"
        :row-actions="rowActions"
        :current-sort="currentSort"
        :current-sort-dir="currentSortDir"
        v-model:selected-ids="selectedIds"
        :components="components"
        :is-loading="isLoading"
        @sort="handleSort"
        @row-action="handleRowAction"
      />
      <component
        :is="resolvedComponents.footer"
        :selected-count="selectedIds.length"
        :total-rows="Array.isArray(data) ? data.length : 0"
        :meta="meta"
        @update:per-page="handlePerPage"
        @page="handlePage"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref, watch, type Component } from 'vue'
import ComponentRegistry from '@raptor/utils/ComponentRegistry'
import { useDataTable, type TableAction, type RowActionPayload } from '@raptor/composables/useDataTable'

export interface TableColumn {
  name: string
  label: string
  sortable?: boolean
  component?: string
}

export interface TableMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from?: number
  to?: number
}

export interface TableComponents {
  header?: string
  filters?: string
  renderer?: string
  footer?: string
  bulkActions?: string
  actions?: string
  dropdownActions?: string
  pagination?: string
  selectable?: string
  summary?: string
}

const props = withDefaults(defineProps<{
  title?: string
  subtitle?: string
  columns?: TableColumn[]
  data?: Array<Record<string, unknown>>
  meta?: TableMeta
  selectable?: boolean
  rowActions?: boolean
  headerActions?: TableAction[]
  bulkActions?: TableAction[]
  components?: TableComponents
  /**
   * Se true, o DataTable gerencia navegação/estado internamente
   * Se false, emite eventos para o componente pai gerenciar
   */
  managed?: boolean
  /**
   * Tempo de debounce para busca (só usado quando managed=true)
   */
  searchDebounce?: number
}>(), {
  selectable: false,
  rowActions: true,
  managed: true,
  searchDebounce: 300,
})

const emit = defineEmits<{
  'header-action': [action: TableAction]
  'filters-reset': []
  'sort': [column: string]
  'row-action': [payload: RowActionPayload]
  'update:per-page': [num: number]
  'page': [num: number]
  'update:selectedIds': [ids: (string | number)[]]
  'update:search': [value: string]
  'bulk-action': [action: TableAction]
}>()

// Usa o composable para gerenciar estado e ações
const {
  search,
  currentSort,
  currentSortDir,
  selectedIds,
  isLoading,
  onSort,
  onPage,
  onPerPage,
  onSearch,
  onFiltersReset,
  onHeaderAction,
  onRowAction,
  onBulkAction,
} = useDataTable({ searchDebounce: props.searchDebounce })

// Computed para verificar se há filtros ativos
const hasActiveFilters = computed(() => {
  return !!search.value || !!currentSort.value
})

// Handlers que decidem entre modo gerenciado e manual
function handleSort(column: string) {
  if (props.managed) {
    onSort(column)
  } else {
    currentSort.value = column
    currentSortDir.value = currentSort.value === column && currentSortDir.value === 'asc' ? 'desc' : 'asc'
    emit('sort', column)
  }
}

function handlePage(page: number) {
  if (props.managed) {
    onPage(page)
  } else {
    emit('page', page)
  }
}

function handlePerPage(perPage: number) {
  if (props.managed) {
    onPerPage(perPage)
  } else {
    emit('update:per-page', perPage)
  }
}

function handleFiltersReset() {
  if (props.managed) {
    onFiltersReset()
  } else {
    search.value = ''
    emit('filters-reset')
  }
}

function handleHeaderAction(action: TableAction) {
  if (props.managed) {
    onHeaderAction(action)
  }
  // Sempre emite para permitir customização
  emit('header-action', action)
}

function handleRowAction(payload: RowActionPayload) {
  if (props.managed) {
    onRowAction(payload)
  }
  emit('row-action', payload)
}

function handleBulkAction(action: TableAction) {
  if (props.managed) {
    onBulkAction(action)
  }
  emit('bulk-action', action)
}

// Watch para busca com debounce quando gerenciado
watch(search, (val) => {
  if (props.managed) {
    onSearch(val)
  } else {
    emit('update:search', val)
  }
})

// Watch para selectedIds - apenas emite, sem loop
watch(selectedIds, (val) => {
  emit('update:selectedIds', val)
}, { deep: true })

const resolvedComponents = computed(() => {
  const defaults = {
    header: 'table-header',
    filters: 'table-filters',
    renderer: 'table-renderer',
    footer: 'table-footer',
    bulkActions: 'table-bulk-action-inline',
  }

  const resolved: Record<string, Component | null> = {}

  for (const [key, defaultName] of Object.entries(defaults)) {
    const componentName = props.components?.[key as keyof TableComponents] ?? defaultName
    resolved[key] = ComponentRegistry.get(componentName) ?? null
  }

  return resolved as {
    header: Component
    filters: Component
    renderer: Component
    footer: Component
    bulkActions: Component
  }
})

// Expõe métodos para uso externo via ref
defineExpose({
  clearSelection: () => { selectedIds.value = [] },
  selectAll: (ids: (string | number)[]) => { selectedIds.value = [...ids] },
  getSelectedIds: () => selectedIds.value,
  bulkAction: (action: TableAction) => {
    if (props.managed) {
      onBulkAction(action)
    }
    emit('bulk-action', action)
  },
})
</script>
