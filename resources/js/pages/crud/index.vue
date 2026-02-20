<template>
  <Head :title="title" />
  <ResourseLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-6 p-6 md:p-8">
      <DataTable
        :title="title"
        :subtitle="subtitle"
        :columns="table?.columns"
        :data="table?.data"
        :meta="table?.meta"
        :selectable="!!table?.selectable"
        :row-actions="true"
        :header-actions="table?.headerActions"
        :bulk-actions="table?.bulkActions"
        :components="table?.components"
      />
    </div>
  </ResourseLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import ResourseLayout from '@raptor/layouts/ResourseLayout.vue'
import DataTable from '@raptor/components/table/DataTable.vue'
import type { BreadcrumbItem } from '@/types'
import { dashboard } from '@/routes'

interface TablePayload {
  columns?: Array<{ name: string; label: string; sortable?: boolean; component?: string }>
  data?: Array<Record<string, unknown> & { _selectId?: string; id?: string; actions?: Record<string, { name: string; label: string; url?: string }> }>
  meta?: { current_page: number; last_page: number; per_page: number; total: number; from?: number; to?: number }
  selectable?: boolean
  headerActions?: Array<{ name: string; label: string; url?: string | null; inertia?: boolean }>
  bulkActions?: Array<{ name: string; label: string; url?: string | null; inertia?: boolean }>
  components?: Record<string, string>
}

const page = usePage()
const table = computed(() => (page.props.table as TablePayload | undefined) ?? null)

const title = computed(() => (page.props.__class__ as string) ?? 'Listagem')
const subtitle = computed(() => '')

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Crud', href: dashboard().url },
  { title: 'Index', href: '#' },
]
</script>

<style scoped></style>
