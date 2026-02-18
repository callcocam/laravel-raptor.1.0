<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #header>
            <slot name="header" />
        </template>
        <slot />
        <template #footer>
            <slot name="footer" />
        </template>
    </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps < Props > (), {
    breadcrumbs: () => [],
});
</script>