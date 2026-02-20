<template>
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="flex flex-1 flex-wrap items-center gap-2">
            <Input
                :model-value="search"
                type="text"
                class="h-8 w-37.5 lg:w-62.5"
                :placeholder="searchPlaceholder"
                @update:model-value="emit('update:search', $event as string)"
            />
            <template v-if="filters?.length">
                <Popover v-for="f in filters" :key="f.name">
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            size="sm"
                            class="h-8 border-dashed"
                        >
                            <PlusCircle class="mr-2 h-4 w-4" />
                            {{ f.label }}
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent :align="'start'" class="w-56 max-h-96 overflow-hidden p-0">
                        <FilterRenderer 
                            :filters="[f]"
                            @apply-filter="handleFilterApply"
                        />
                    </PopoverContent>
                </Popover>
            </template>
            <Button
                v-if="hasActiveFilters || search"
                variant="ghost"
                size="sm"
                class="h-8 px-2 lg:px-3"
                @click="reset"
            >
                Reset
                <XCircle class="ml-2 h-4 w-4" />
            </Button>
        </div>
        <slot name="view" />
    </div>
</template>

<script lang="ts" setup>
import { PlusCircle, XCircle } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'
import FilterRenderer from './filters/FilterRenderer.vue'
import type { Filter } from '@raptor/types'

defineProps<{
    search?: string
    searchPlaceholder?: string
    filters?: Filter[]
    hasActiveFilters?: boolean
}>()

const emit = defineEmits<{
    (e: 'update:search', value: string): void
    (e: 'apply-filter', filterName: string, value: any): void
    (e: 'reset'): void
}>()

function handleFilterApply(filterName: string, value: any) {
    emit('apply-filter', filterName, value)
}

function reset() {
    emit('update:search', '')
    emit('reset')
}
</script>
