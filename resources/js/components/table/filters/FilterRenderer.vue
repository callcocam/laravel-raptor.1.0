<template>
    <div v-if="filters && filters.length > 0">
        <component
            v-for="filter in filters"
            :key="`${filter.name}-${filter.component}`"
            :is="resolvedComponent(filter.component)"
            :filter="filter"
            v-bind="filter.props"
            @apply="handleFilterApply(filter.name, $event)"
        />
    </div>
</template>
<script lang="ts" setup>
import { inject, computed } from 'vue'
import type { ComponentRegistry } from '@raptor/utils/ComponentRegistry'

interface Filter {
    name: string
    label: string
    component?: string
    props?: Record<string, any>
    options?: Array<{ label: string; value: string | number }>
    [key: string]: any
}

const props = defineProps<{
    filters?: Filter[]
}>()

const emit = defineEmits<{
    (e: 'apply-filter', filterName: string, value: any): void
}>()

// Injetar o registry de componentes
const registry = inject<typeof ComponentRegistry>('componentRegistry')

const resolvedComponent = computed(() => {
    return (componentName?: string) => {
        if (!componentName || !registry) {
            return null
        }
        
        const component = registry.get(componentName)
        if (!component) {
            console.warn(`[FilterRenderer] Component "${componentName}" not found in registry`)
            return null
        }
        return component
    }
})

function handleFilterApply(filterName: string, value: any) {
    emit('apply-filter', filterName, value)
}
</script>
