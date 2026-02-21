<template>
    <Head title="Edit" />
    <ResourseLayout :breadcrumbs="breadcrumbs">
        <h1>Edit</h1>
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