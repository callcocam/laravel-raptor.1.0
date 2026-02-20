<template>
  <div class="rounded-md border">
    <div class="relative w-full overflow-auto">
      <table class="w-full caption-bottom text-sm">
        <thead class="[&_tr]:border-b">
          <tr class="border-b transition-colors">
            <th
              v-if="selectable"
              class="h-10 px-2 text-left align-middle font-medium whitespace-nowrap [&:has([role=checkbox])]:pr-0 w-10"
            >
              <button
                type="button"
                role="checkbox"
                :aria-checked="isAllSelected"
                class="size-4 shrink-0 rounded border border-input translate-y-0.5"
                @click="toggleSelectAll"
              />
            </th>
            <th
              v-for="col in columns"
              :key="col.name"
              class="h-10 px-2 text-left align-middle font-medium whitespace-nowrap"
            >
              <button
                v-if="col.sortable"
                type="button"
                class="inline-flex items-center gap-2 -ml-3 h-8 px-3 text-sm font-medium hover:bg-accent rounded-md"
                @click="emit('sort', col.name)"
              >
                {{ col.label }}
                <span v-if="currentSort === col.name" class="text-muted-foreground">
                  {{ currentSortDir === 'desc' ? '↓' : '↑' }}
                </span>
              </button>
              <span v-else>{{ col.label }}</span>
            </th>
            <th v-if="hasRowActions" class="h-10 px-2 w-10" />
          </tr>
        </thead>
        <tbody class="[&_tr:last-child]:border-0">
            <tr
            v-for="row in data"
            :key="rowId(row)"
            class="border-b transition-colors hover:bg-muted/50"
          >
            <td
              v-if="selectable"
              class="p-2 align-middle whitespace-nowrap [&:has([role=checkbox])]:pr-0"
            >
              <button
                type="button"
                role="checkbox"
                :aria-checked="selectedIds.includes(rowId(row))"
                class="size-4 shrink-0 rounded border border-input translate-y-0.5"
                @click="toggleRow(rowId(row))"
              />
            </td>
            <td
              v-for="col in columns"
              :key="col.name"
              class="p-2 align-middle whitespace-nowrap"
            >
              <ColumnRenderer :record="row" :column="col" />
            </td>
            <td v-if="hasRowActions" class="p-2 align-middle whitespace-nowrap">
              <div v-if="rowActionsList(row).length" class="relative">
                <button
                  type="button"
                  class="flex h-8 w-8 items-center justify-center rounded-md hover:bg-accent"
                  aria-haspopup="true"
                  :aria-expanded="openRowMenu === rowId(row)"
                  @click="openRowMenu = openRowMenu === rowId(row) ? null : rowId(row)"
                >
                  &#8230;
                </button>
                <div
                  v-if="openRowMenu === rowId(row)"
                  class="absolute right-0 top-full z-10 mt-1 min-w-[120px] rounded-md border bg-background py-1 shadow-lg"
                >
                  <a
                    v-for="act in rowActionsList(row)"
                    :key="act.name"
                    :href="act.url || '#'"
                    class="block px-3 py-1.5 text-sm hover:bg-accent"
                    @click.prevent="emit('rowAction', { row, action: act })"
                  >
                    {{ act.label }}
                  </a>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import ColumnRenderer from './types/ColumnRenderer.vue'

const props = withDefaults(
  defineProps<{
    columns: Array<{ name: string; label: string; sortable?: boolean; component?: string }>
    data: Array<Record<string, unknown> & { _selectId?: string; id?: string; actions?: Record<string, { name: string; label: string; url?: string }> }>
    selectable?: boolean
    rowActions?: boolean
    currentSort?: string | null
    currentSortDir?: 'asc' | 'desc'
    selectedIds?: (string | number)[]
  }>(),
  {
    selectable: false,
    rowActions: true,
    currentSort: null,
    currentSortDir: 'asc',
    selectedIds: () => [],
  }
)

const emit = defineEmits<{
  (e: 'update:selectedIds', value: (string | number)[]): void
  (e: 'sort', column: string): void
  (e: 'rowAction', payload: { row: Record<string, unknown>; action: { name: string; label: string; url?: string } }): void
}>()

const openRowMenu = ref<string | number | null>(null)

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
