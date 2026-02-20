<template>
  <div class="flex items-center gap-2">
    <DynamicIcon v-if="column.icon" :name="column.icon" class="h-4 w-4 shrink-0" />
    <span
      :class="[
        truncateClass,
        badgeClass,
      ]"
    >
      {{ displayValue }}
    </span>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import DynamicIcon from '@raptor/components/ui/DynamicIcon.vue'

interface Column {
  name: string
  label?: string
  limit?: number | null
  icon?: string
  prefix?: string
  suffix?: string
  isBadge?: boolean
  color?: string
  [key: string]: unknown
}

const props = defineProps<{
  record: Record<string, unknown>
  column: Column
}>()

const cellValue = computed(() => {
  // First try the full dot-notation key as-is
  if (props.column.name in props.record) {
    const value = props.record[props.column.name]
    return value ? String(value) : ''
  }

  // Fallback to nested object traversal
  const keys = props.column.name.split('.')
  let value: unknown = props.record

  for (const key of keys) {
    if (value && typeof value === 'object' && key in value) {
      value = (value as Record<string, unknown>)[key]
    } else {
      value = null
      break
    }
  }

  return value ? String(value) : ''
})

const displayValue = computed(() => {
  if (!cellValue.value) return '-'

  const prefix = props.column.prefix ? `${props.column.prefix} ` : ''
  const suffix = props.column.suffix ? ` ${props.column.suffix}` : ''

  return `${prefix}${cellValue.value}${suffix}`
})

const truncateClass = computed(() =>
  props.column.limit ? 'max-w-[500px] truncate block' : ''
)

const badgeClass = computed(() => {
  if (!props.column.isBadge) return ''

  const colors: Record<string, string> = {
    default: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
    primary: 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100',
    success: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
    danger: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
    warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
    info: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-700 dark:text-cyan-100',
  }

  const colorClass = colors[props.column.color as string] || colors.default

  return [
    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
    colorClass,
  ]
})
</script>
