<template>
    <div class="flex items-center">
        <span :title="fullDateTime">
            {{ displayValue }}
        </span>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

interface Column {
    name: string;
    label: string;
    format?: string;
    timezone?: string;
    showDate?: boolean;
    showTime?: boolean;
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

const displayValue = computed(() => {
    return cellValue.value || '-';
});

const fullDateTime = computed(() => {
    if (cellValue.value && typeof cellValue.value === 'string') {
        try {
            const date = new Date(cellValue.value);
            return date.toLocaleString();
        } catch {
            return cellValue.value;
        }
    }
    return '';
});
</script>
