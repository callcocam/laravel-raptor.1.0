<template>
    <div class="flex items-center gap-2">
        <div
            v-if="column.isDot"
            :style="{ backgroundColor: badgeColor }"
            class="h-3 w-3 rounded-full"
            :title="displayText"
        />
        <div v-else :class="badgeClasses">
            <DynamicIcon v-if="column.icon" :name="column.icon" class="h-3.5 w-3.5" />
            <span v-if="!column.isDot">{{ displayText }}</span>
        </div>
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
    isDot?: boolean;
    color?: string;
    colorMap?: Record<string, string>;
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

const badgeColor = computed(() => {
    if (!cellValue.value) return '#9CA3AF';
    
    if (props.column.colorMap && props.column.colorMap[cellValue.value]) {
        return props.column.colorMap[cellValue.value];
    }
    
    return props.column.color || '#6366F1';
});

const badgeClasses = computed(() => {
    const colorMap: Record<string, string> = {
        'default': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
        'primary': 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100',
        'success': 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
        'danger': 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
        'warning': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
        'info': 'bg-cyan-100 text-cyan-800 dark:bg-cyan-700 dark:text-cyan-100',
        'purple': 'bg-purple-100 text-purple-800 dark:bg-purple-700 dark:text-purple-100',
    };
    
    // Tenta usar o valor como chave para colorMap
    let colorClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100';
    
    if (cellValue.value && props.column.colorMap && props.column.colorMap[cellValue.value]) {
        // Se há um mapa de cores, usar a cor do mapa
        const color = props.column.colorMap[cellValue.value];
        colorClass = colorMap[color] || colorClass;
    } else if (props.column.color && colorMap[props.column.color]) {
        colorClass = colorMap[props.column.color];
    }
    
    return [
        'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium',
        colorClass,
    ];
});
</script>
