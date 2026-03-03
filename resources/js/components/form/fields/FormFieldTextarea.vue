<template>
  <div class="space-y-2">
    <Textarea
      :id="field.name"
      :value="modelValue ?? ''"
      :placeholder="(field.placeholder as string) ?? undefined"
      :rows="(field.rows as number) ?? 4"
      class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:opacity-50 md:text-sm"
      @input="emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
    />
  </div>
</template>

<script lang="ts" setup>
import type { FormField } from '@raptor/types'


defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()
</script>
