<template>
  <div class="flex items-center justify-between gap-2 flex-wrap">
    <div class="flex flex-1 items-center gap-2 flex-wrap">
      <Input
        :model-value="search"
        type="text"
        class="h-8 w-[150px] lg:w-[250px]"
        :placeholder="searchPlaceholder"
        @update:model-value="emit('update:search', $event)"
      />
      <template v-if="filters?.length">
        <Button
          v-for="f in filters"
          :key="f.name"
          variant="outline"
          size="sm"
          class="h-8 border-dashed"
        >
          <PlusCircle class="mr-2 h-4 w-4" />
          {{ f.label }}
        </Button>
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

defineProps<{
  search?: string
  searchPlaceholder?: string
  filters?: Array<{ name: string; label: string }>
  hasActiveFilters?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:search', value: string): void
  (e: 'reset'): void
}>()

function reset() {
  emit('update:search', '')
  emit('reset')
}
</script>
