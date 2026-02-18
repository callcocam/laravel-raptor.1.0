<template>
    <Head title="Create" />
    <ResourseLayout :breadcrumbs="breadcrumbs">
        <h1>Create</h1>
    </ResourseLayout>
</template>

<script setup lang="ts">
import ResourseLayout from '@raptor/layouts/ResourseLayout.vue';
import { Head } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Crud',
        href: dashboard().url,
    },
];
</script>

<style scoped>

</style>