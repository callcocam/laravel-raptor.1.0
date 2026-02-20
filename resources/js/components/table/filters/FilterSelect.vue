<template>
    <div class="flex flex-col p-1" style="max-height: 360px">
        <!-- Search Input -->
        <div class="mb-2">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar opções..."
                class="w-full rounded-md border border-input bg-background px-2 py-1 text-sm placeholder:text-muted-foreground focus:ring-2 focus:ring-ring focus:outline-none"
            />
        </div>

        <!-- Options List with Scroll (flex-1 to fill remaining space) -->
        <div class="min-h-0 flex-1 overflow-y-auto rounded-md border">
            <template v-if="filteredOptions.length > 0">
                <label
                    v-for="option in filteredOptions"
                    :key="option.value"
                    class="flex cursor-pointer items-center gap-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <input
                        type="checkbox"
                        :checked="isSelected(option.value)"
                        @change="toggleOption(option.value)"
                        class="h-4 w-4 shrink-0 cursor-pointer rounded border-gray-300"
                    />
                    <span class="text-sm">{{ option.label }}</span>
                </label>
            </template>
            <div v-else class="px-2 py-4 text-center text-sm text-gray-500">
                Nenhuma opção encontrada
            </div>
        </div>

        <!-- Selected tags (only when items selected) -->
        <div v-if="selectedValues.length > 0" class="mt-2 flex flex-col gap-1">
            <div class="flex flex-wrap items-center gap-1">
                <span
                    v-for="val in selectedValues"
                    :key="val"
                    class="inline-flex items-center gap-1 rounded bg-blue-50 px-2 py-0.5 text-xs text-blue-700"
                >
                    {{ getOptionLabel(val) }}
                    <button
                        type="button"
                        @click="toggleOption(val)"
                        class="shrink-0 hover:text-blue-900"
                    >
                        ✕
                    </button>
                </span>
            </div>

            <!-- Action Buttons (always visible, pinned to bottom) -->
            <div class="mt-2 flex gap-2 border-t pt-2">
                <button
                    type="button"
                    @click="clearAll"
                    class="flex-1 rounded border border-gray-200 py-1.5 text-xs text-gray-600 hover:bg-gray-100"
                >
                    Limpar
                </button>
                <button
                    type="button"
                    @click="applyFilter"
                    class="flex-1 rounded bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700"
                >
                    Aplicar
                </button>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue';
import type { Filter } from '@raptor/types';

const props = defineProps<{
    filter: Filter;
}>();

const emit = defineEmits<{
    (e: 'apply', value: string | number | (string | number)[] | null): void;
}>();

const searchQuery = ref('');

// Initialize selectedValues from URL query params
function getInitialValues(): (string | number)[] {
    if (typeof window === 'undefined') return [];
    const params = new URLSearchParams(window.location.search);
    const urlValue = params.get(props.filter.name);
    if (!urlValue) return [];
    return urlValue
        .split(',')
        .map((v) => v.trim())
        .filter(Boolean);
}

const selectedValues = ref<(string | number)[]>(getInitialValues());

// Filter options based on search query
const filteredOptions = computed(() => {
    if (!props.filter.options) return [];

    return props.filter.options.filter((option) =>
        option.label.toLowerCase().includes(searchQuery.value.toLowerCase()),
    );
});

function isSelected(value: string | number): boolean {
    return selectedValues.value.includes(value);
}

function toggleOption(value: string | number): void {
    const index = selectedValues.value.indexOf(value);
    if (index > -1) {
        selectedValues.value.splice(index, 1);
    } else {
        selectedValues.value.push(value);
    }
}

function getOptionLabel(value: string | number): string {
    const option = props.filter.options?.find((opt) => opt.value === value);
    return option?.label || String(value);
}

function clearAll(): void {
    selectedValues.value = [];
    emit('apply', null);
}

function applyFilter(): void {
    if (selectedValues.value.length === 0) {
        emit('apply', null);
        return;
    }
    const value =
        selectedValues.value.length === 1
            ? selectedValues.value[0]
            : selectedValues.value.join(',');
    emit('apply', value);
}
</script>
