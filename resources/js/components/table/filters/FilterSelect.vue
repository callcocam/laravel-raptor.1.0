<template>
    <div class="flex flex-col gap-2 p-2" style="max-height: 360px">
        <!-- Search Input -->
        <Input v-model="searchQuery" placeholder="Buscar opções..." class="h-8 text-sm" />

        <!-- Options List with Scroll -->
        <div class="min-h-0 flex-1 overflow-y-auto rounded-md border">
            <template v-if="filteredOptions.length > 0">
                <div
                    v-for="option in filteredOptions"
                    :key="option.value"
                    class="flex cursor-pointer items-center gap-2 px-2 py-1.5 hover:bg-accent"
                    @click="toggleOption(option.value)"
                >
                    <Checkbox
                        :id="`opt-${option.value}`"
                        :checked="isSelected(option.value)"
                        @update:checked="toggleOption(option.value)"
                        @click.stop
                    />
                    <Label :for="`opt-${option.value}`" class="cursor-pointer text-sm font-normal">
                        {{ option.label }}
                    </Label>
                </div>
            </template>
            <div v-else class="px-2 py-4 text-center text-sm text-muted-foreground">
                Nenhuma opção encontrada
            </div>
        </div>

        <!-- Selected tags -->
        <div v-if="selectedValues.length > 0" class="flex flex-wrap gap-1">
            <Badge
                v-for="val in selectedValues"
                :key="val"
                variant="secondary"
                class="cursor-pointer gap-1"
                @click="toggleOption(val)"
            >
                {{ getOptionLabel(val) }}
                <X class="h-3 w-3" />
            </Badge>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 border-t pt-2">
            <Button type="button" variant="outline" size="sm" class="flex-1" @click="clearAll">
                Limpar
            </Button>
            <Button type="button" size="sm" class="flex-1" @click="applyFilter"> Aplicar </Button>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { X } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import type { Filter } from '@raptor/types'

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
