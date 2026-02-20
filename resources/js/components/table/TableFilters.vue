<template>
  <div class="flex items-center justify-between gap-2 flex-wrap">
    <div class="flex flex-1 items-center gap-2 flex-wrap">
      <input
        :value="search"
        type="text"
        class="h-8 w-[150px] lg:w-[250px] rounded-md border bg-transparent px-3 py-1 text-sm placeholder:text-muted-foreground"
        :placeholder="searchPlaceholder"
        @input="emit('update:search', ($event.target as HTMLInputElement).value)"
      />
      <template v-if="filters?.length">
        <button
          v-for="f in filters"
          :key="f.name"
          type="button"
          class="inline-flex items-center gap-1.5 rounded-md border border-dashed px-3 py-1.5 h-8 text-sm font-medium"
        >
          + {{ f.label }}
        </button>
      </template>
      <button
        v-if="hasActiveFilters || search"
        type="button"
        class="inline-flex items-center gap-2 px-2 lg:px-3 h-8 text-sm font-medium"
        @click="reset"
      >
        Reset
      </button>
    </div>
    <slot name="view" />
  </div>
</template>

<script lang="ts" setup>
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
