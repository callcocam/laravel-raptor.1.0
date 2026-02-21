<template>
  <Head title="Editar" />
  <ResourseLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-6 p-6 md:p-8">
      <DataForm :form="form" />
    </div>
  </ResourseLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import ResourseLayout from '@raptor/layouts/ResourseLayout.vue'
import DataForm from '@raptor/components/form/DataForm.vue'
import type { BreadcrumbItem } from '@/types'
import type { FormPayload } from '@raptor/types'

const page = usePage()
const breadcrumbs = computed(
  () => (page.props.breadcrumbs as BreadcrumbItem[] | undefined) ?? []
)
const form = computed(() => (page.props.form as FormPayload | undefined) ?? null)
</script>

<style scoped>

</style>