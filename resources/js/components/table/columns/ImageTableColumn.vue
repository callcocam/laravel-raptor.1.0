<template>
    <div class="flex items-center">
        <img
            :src="imageSrc"
            :alt="imageAlt"
            :width="column.imageWidth"
            :height="column.imageHeight"
            :class="[
                'object-cover',
                column.isRounded && 'rounded-full'
            ]"
        />
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

interface Column {
    name: string;
    label: string;
    imageWidth?: number;
    imageHeight?: number;
    isRounded?: boolean;
    fallback?: string;
    [key: string]: unknown;
}

const props = defineProps<{
    record: Record<string, unknown>;
    column: Column;
}>();

const cellValue = computed(() => {
    // First try the full dot-notation key as-is
    if (props.column.name in props.record) {
        return props.record[props.column.name];
    }

    // Fallback to nested object traversal
    const keys = props.column.name.split('.');
    let value: unknown = props.record;

    for (const key of keys) {
        if (value && typeof value === 'object' && key in value) {
            value = (value as Record<string, unknown>)[key];
        } else {
            value = null;
            break;
        }
    }

    return value;
});

const imageSrc = computed(() => {
    if (cellValue.value && typeof cellValue.value === 'string') {
        return cellValue.value;
    }
    return props.column.fallback || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"%3E%3Crect fill="%23ddd" width="100" height="100"/%3E%3Ctext x="50" y="50" font-size="12" text-anchor="middle" dominant-baseline="middle" fill="%23999"%3ENo Image%3C/text%3E%3C/svg%3E';
});

const imageAlt = computed(() => {
    return `${props.column.label} - ${props.record.id || 'N/A'}`;
});
</script>
