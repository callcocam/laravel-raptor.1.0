<template>
    <Input
        :id="field.name"
        :data-slot="inInputGroup ? 'input-group-control' : undefined"
        :model-value="
            typeof modelValue === 'string' || typeof modelValue === 'number'
                ? modelValue
                : ''
        "
        :type="(field.inputType as string) ?? 'text'"
        :placeholder="(field.placeholder as string) ?? undefined"
        :class="cn('w-full', inInputGroup && 'flex-1 rounded-none border-0 shadow-none focus-visible:ring-0 dark:bg-transparent')"
        @update:model-value="emit('update:modelValue', $event)"
    />
</template>

<script lang="ts" setup>
import { inject } from 'vue';
import { cn } from '@/lib/utils';
import { Input } from '@/components/ui/input';
import type { ComputedRef } from 'vue';
import type { FormField } from '@raptor/types';

defineProps<{
    field: FormField;
    modelValue: unknown;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: unknown];
}>();

const inInputGroup = inject<ComputedRef<boolean>>('inInputGroup', { value: false } as ComputedRef<boolean>)
</script>
