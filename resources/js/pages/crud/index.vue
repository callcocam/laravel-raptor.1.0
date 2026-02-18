<template>

    <Head title="Index" />
    <ResourseLayout :breadcrumbs="breadcrumbs">
        <h1>Index</h1>
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
    {
        title: 'Index',
        href: '#',
    },
];
</script>

<style scoped></style>