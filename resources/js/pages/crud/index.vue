<template>
  <Head :title="title" />
  <ResourseLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-6 p-6 md:p-8">
      <TableHeader
        :title="title"
        :subtitle="subtitle"
        :header-actions="table?.headerActions"
        @action="onHeaderAction"
      />
      <div class="space-y-4">
        <TableFilters
          v-model:search="search"
          search-placeholder="Filtrar..."
          :has-active-filters="false"
          @reset="onFiltersReset"
        />
        <TableRenderer
          v-if="table?.columns && table?.data"
          :columns="table.columns"
          :data="table.data"
          :selectable="!!table.selectable"
          :row-actions="true"
          :current-sort="currentSort"
          :current-sort-dir="currentSortDir"
          v-model:selected-ids="selectedIds"
          @sort="onSort"
          @row-action="onRowAction"
        />
        <TableFooter
          v-if="table"
          :selected-count="selectedIds.length"
          :total-rows="Array.isArray(table.data) ? table.data.length : 0"
          :meta="table.meta"
          @update:per-page="onPerPage"
          @page="onPage"
        />
      </div>
    </div>
  </ResourseLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import ResourseLayout from '@raptor/layouts/ResourseLayout.vue'
import TableHeader from '@raptor/components/table/TableHeader.vue'
import TableFilters from '@raptor/components/table/TableFilters.vue'
import TableRenderer from '@raptor/components/table/TableRenderer.vue'
import TableFooter from '@raptor/components/table/TableFooter.vue'
import type { BreadcrumbItem } from '@/types'
import { dashboard } from '@/routes'

interface TablePayload {
  columns?: Array<{ name: string; label: string; sortable?: boolean; component?: string }>
  data?: Array<Record<string, unknown> & { _selectId?: string; id?: string; actions?: Record<string, { name: string; label: string; url?: string }> }>
  meta?: { current_page: number; last_page: number; per_page: number; total: number; from?: number; to?: number }
  selectable?: boolean
  headerActions?: Array<{ name: string; label: string; url?: string | null; inertia?: boolean }>
}

const page = usePage()
const table = computed(() => (page.props.table as TablePayload | undefined) ?? null)

const title = computed(() => (page.props.__class__ as string) ?? 'Listagem')
const subtitle = computed(() => '')

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Crud', href: dashboard().url },
  { title: 'Index', href: '#' },
]

function getQuery() {
  const params = new URLSearchParams(window.location.search)
  return {
    search: params.get('search') ?? '',
    sort: params.get('sort') ?? null,
    sort_dir: (params.get('sort_dir') === 'desc' ? 'desc' : 'asc') as 'asc' | 'desc',
  }
}

const q = getQuery()
const search = ref(q.search)
const selectedIds = ref<(string | number)[]>([])
const currentSort = ref<string | null>(q.sort)
const currentSortDir = ref<'asc' | 'desc'>(q.sort_dir)

function buildUrl(updates: Record<string, string | number | null>) {
  const url = new URL(window.location.href)
  Object.entries(updates).forEach(([key, value]) => {
    if (value === null || value === '') {
      url.searchParams.delete(key)
    } else {
      url.searchParams.set(key, String(value))
    }
  })
  return url.pathname + '?' + url.searchParams.toString()
}

function onSort(column: string) {
  const nextDir = currentSort.value === column && currentSortDir.value === 'asc' ? 'desc' : 'asc'
  currentSort.value = column
  currentSortDir.value = nextDir
  router.get(buildUrl({ sort: column, sort_dir: nextDir, page: 1 }))
}

function onPage(num: number) {
  router.get(buildUrl({ page: num }))
}

function onPerPage(num: number) {
  router.get(buildUrl({ per_page: num, page: 1 }))
}

function onFiltersReset() {
  search.value = ''
  router.get(buildUrl({ search: null, page: 1 }))
}

function onHeaderAction(action: { url?: string | null }) {
  if (action.url) {
    router.get(action.url)
  }
}

function onRowAction(payload: { action: { url?: string } }) {
  if (payload.action.url) {
    router.get(payload.action.url)
  }
}

let searchDebounce: ReturnType<typeof setTimeout> | null = null
watch(search, (val) => {
  if (searchDebounce) clearTimeout(searchDebounce)
  searchDebounce = setTimeout(() => {
    router.get(buildUrl({ search: val || null, page: 1 }))
  }, 300)
})
</script>

<style scoped></style>
