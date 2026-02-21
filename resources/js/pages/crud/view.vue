<template>
    <Head title="View" />
    <ResourseLayout :breadcrumbs="breadcrumbs">
        <h1>View</h1>
    </ResourseLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import ResourseLayout from '@raptor/layouts/ResourseLayout.vue'
import type { BreadcrumbItem } from '@/types'

const page = usePage()
const breadcrumbs = computed(
  () => (page.props.breadcrumbs as BreadcrumbItem[] | undefined) ?? []
)
</script>

<style scoped>

</style>