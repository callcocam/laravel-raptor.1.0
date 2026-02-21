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
        :filters="table?.filters"
        :components="table?.components"
        :summary="table?.summary"
        :summary-position="table?.summaryPosition ?? 'top'"
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
import type { TablePayload } from '@raptor/types'
import { dashboard } from '@/routes'

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
