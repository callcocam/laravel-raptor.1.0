<template>
  <component
    v-if="resolvedComponent"
    :is="resolvedComponent"
    :record="record"
    :column="column"
  />
  <span v-else :class="truncateClass">{{ rawValue }}</span>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import ComponentRegistry from '@raptor/utils/ComponentRegistry'

const props = defineProps<{
  record: Record<string, unknown>
  column: { name: string; component?: string; limit?: number | null }
}>()

const resolvedComponent = computed(() => {
  const name = props.column.component || 'text-table-column'
  return ComponentRegistry.get(name) ?? ComponentRegistry.get('text-table-column')
})

const rawValue = computed(() => {
  const v = props.record[props.column.name]
  return v === null || v === undefined ? '' : String(v)
})

const truncateClass = computed(() =>
  props.column.limit ? 'max-w-[500px] truncate block' : ''
)
</script>