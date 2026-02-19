<template>

    <Head title="Index" />
    <ResourseLayout :breadcrumbs="breadcrumbs">
        <h1>{{ __class__ }}</h1>
    </ResourseLayout>
</template>

<script setup lang="ts">
import ResourseLayout from '@raptor/layouts/ResourseLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

const __class__ = usePage().props.__class__;

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