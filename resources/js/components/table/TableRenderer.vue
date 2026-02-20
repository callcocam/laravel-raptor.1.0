<template>
  <div class="rounded-md border">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead v-if="selectable" class="w-10">
            <Checkbox
              :model-value="isAllSelected"
              @update:model-value="toggleSelectAll"
            />
          </TableHead>
          <TableHead v-for="col in columns" :key="col.name">
            <Button
              v-if="col.sortable"
              variant="ghost"
              class="-ml-3 h-8 px-3 w-full justify-between"
              @click="emit('sort', col.name)"
            >
              {{ col.label }}
              <span v-if="currentSort === col.name" class="ml-2 text-muted-foreground">
                {{ currentSortDir === 'desc' ? '↓' : '↑' }}
              </span>
            </Button>
            <span v-else>{{ col.label }}</span>
          </TableHead>
          <TableHead v-if="hasRowActions" class="w-10" />
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow
          v-for="row in data"
          :key="rowId(row)"
          :data-state="selectedIds.includes(rowId(row)) ? 'selected' : undefined"
        >
          <TableCell v-if="selectable">
            <Checkbox
              :model-value="selectedIds.includes(rowId(row))"
              @update:model-value="toggleRow(rowId(row))"
            />
          </TableCell>
          <TableCell v-for="col in columns" :key="col.name">
            <ColumnRenderer :record="row" :column="col" />
          </TableCell>
          <TableCell v-if="hasRowActions">
            <TableActionRenderer
              :component="tableComponents.actions"
              :record="row"
              @action="(a) => emit('rowAction', { row, action: a })"
            />
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Checkbox } from '@/components/ui/checkbox'
import { Button } from '@/components/ui/button' 
import ColumnRenderer from './types/ColumnRenderer.vue'
import TableActionRenderer from './TableActionRenderer.vue'

const defaultComponents = {
  actions: 'table-action-inline',
  filters: 'table-filter-inline',
  headerActions: 'table-header-action-inline',
  bulkActions: 'table-bulk-action-inline',
  summary: 'table-summary-inline',
  pagination: 'table-pagination-inline',
  selectable: 'table-selectable-inline',
  dropdownActions: 'table-dropdown-action-inline',
}

const props = withDefaults(
  defineProps<{
    columns: Array<{ name: string; label: string; sortable?: boolean; component?: string }>
    data: Array<Record<string, unknown> & { _selectId?: string; id?: string; actions?: Record<string, { name: string; label: string; url?: string }> }>
    selectable?: boolean
    rowActions?: boolean
    currentSort?: string | null
    currentSortDir?: 'asc' | 'desc'
    selectedIds?: (string | number)[]
    components?: Record<string, string>
  }>(),
  {
    selectable: false,
    rowActions: true,
    currentSort: null,
    currentSortDir: 'asc',
    selectedIds: () => [],
    components: () => ({}),
  }
)

const tableComponents = computed(() => ({ ...defaultComponents, ...props.components }))

const emit = defineEmits<{
  (e: 'update:selectedIds', value: (string | number)[]): void
  (e: 'sort', column: string): void
  (e: 'rowAction', payload: { row: Record<string, unknown>; action: { name: string; label: string; url?: string } }): void
}>()

function rowId(row: { _selectId?: string; id?: string }): string | number {
  return row._selectId ?? row.id ?? ''
}

const hasRowActions = computed(() => props.rowActions && props.data.some((r) => rowActionsList(r).length > 0))

function rowActionsList(row: { actions?: Record<string, { name: string; label: string; url?: string }> }) {
  if (!row.actions) return []
  return Object.values(row.actions)
}

const isAllSelected = computed(() => {
  if (!props.selectable || !props.data.length) return false
  const ids = props.data.map(rowId).filter((id) => id !== '')
  return ids.length > 0 && ids.every((id) => props.selectedIds?.includes(id))
})

function toggleSelectAll() {
  if (!props.selectable) return
  const ids = props.data.map(rowId).filter((id) => id !== '') as (string | number)[]
  if (isAllSelected.value) {
    emit('update:selectedIds', (props.selectedIds ?? []).filter((id) => !ids.includes(id)))
  } else {
    const merged = new Set([...(props.selectedIds ?? []), ...ids])
    emit('update:selectedIds', [...merged])
  }
}

function toggleRow(id: string | number) {
  if (!props.selectable) return
  const current = props.selectedIds ?? []
  const next = current.includes(id) ? current.filter((x) => x !== id) : [...current, id]
  emit('update:selectedIds', next)
}
</script>
