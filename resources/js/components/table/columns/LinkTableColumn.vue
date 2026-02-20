<template>
    <div class="flex items-center gap-1">
        <DynamicIcon v-if="column.icon" :name="column.icon" class="h-4 w-4" />
        <a
            v-if="href"
            :href="href"
            :target="column.openInNewTab ? '_blank' : '_self'"
            rel="noopener noreferrer"
            class="font-medium text-blue-600 hover:text-blue-800 hover:underline dark:text-blue-400 dark:hover:text-blue-300"
        >
            {{ displayText }}
        </a>
        <span v-else class="text-muted-foreground">
            {{ cellValue || '-' }}
        </span>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import DynamicIcon from '@raptor/components/ui/DynamicIcon.vue';

interface Column {
    name: string;
    label: string;
    icon?: string;
    prefix?: string;
    suffix?: string;
    openInNewTab?: boolean;
    [key: string]: unknown;
}

const props = defineProps<{
    record: Record<string, unknown>;
    column: Column;
}>();

const cellValue = computed(() => {
    // First try the full dot-notation key as-is
    if (props.column.name in props.record) {
        const value = props.record[props.column.name];
        return value ? String(value) : null;
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

    return value ? String(value) : null;
});

const displayText = computed(() => {
    if (!cellValue.value) return '-';
    
    const prefix = props.column.prefix ? `${props.column.prefix} ` : '';
    const suffix = props.column.suffix ? ` ${props.column.suffix}` : '';
    
    return `${prefix}${cellValue.value}${suffix}`;
});

// NOTE: A URL deve ser gerada no backend e passada via props
// Ex: column.getUrl(value) ou ainda implementar no backend
const href = computed(() => {
    // Por enquanto, usar o valor como URL se começar com http ou /
    if (cellValue.value && (cellValue.value.startsWith('http') || cellValue.value.startsWith('/'))) {
        return cellValue.value;
    }
    return null;
});
</script>
