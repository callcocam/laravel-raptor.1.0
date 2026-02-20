<template>
  <span :class="truncateClass">{{ displayValue }}</span>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

const props = defineProps<{
  record: Record<string, unknown>
  column: { name: string; limit?: number | null }
}>()

const displayValue = computed(() => {
  const value = props.record[props.column.name]
  if (value === null || value === undefined) return ''
  return String(value)
})

const truncateClass = computed(() =>
  props.column.limit ? 'max-w-[500px] truncate block' : ''
)
</script>
