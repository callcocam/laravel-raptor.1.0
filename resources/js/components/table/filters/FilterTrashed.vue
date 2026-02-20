<template>
    <div class="flex flex-col gap-2 p-2">
        <!-- Options as toggle buttons -->
        <div class="flex flex-col gap-1 rounded-md border p-1">
            <Button
                type="button"
                :variant="selectedValue === '' ? 'default' : 'ghost'"
                size="sm"
                class="justify-start gap-2"
                @click="selectedValue = ''"
            >
                <Eye class="h-4 w-4" />
                Sem excluídos
            </Button>
            <Button
                v-for="option in filter.options"
                :key="option.value"
                type="button"
                :variant="selectedValue === String(option.value) ? 'default' : 'ghost'"
                size="sm"
                class="justify-start gap-2"
                @click="selectedValue = String(option.value)"
            >
                <component :is="optionIcon(String(option.value))" class="h-4 w-4" />
                {{ option.label }}
            </Button>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 border-t pt-2">
            <Button type="button" variant="outline" size="sm" class="flex-1" @click="clearFilter">
                Limpar
            </Button>
            <Button type="button" size="sm" class="flex-1" @click="applyFilter"> Aplicar </Button>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { Eye, Trash2, ArchiveRestore } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import type { Filter } from '@raptor/types'

const props = defineProps<{
    filter: Filter
}>()

const emit = defineEmits<{
    (e: 'apply', value: string | null): void
}>()

// Initialize from URL query param
function getInitialValue(): string {
    if (typeof window === 'undefined') return ''
    const params = new URLSearchParams(window.location.search)
    return params.get(props.filter.name) ?? ''
}

const selectedValue = ref<string>(getInitialValue())

function optionIcon(value: string) {
    return value === 'with' ? ArchiveRestore : Trash2
}

function clearFilter(): void {
    selectedValue.value = ''
    emit('apply', null)
}

function applyFilter(): void {
    emit('apply', selectedValue.value || null)
}
</script>
