<template>
  <div class="flex items-center justify-between px-2 py-2">
    <div class="flex-1 text-sm text-muted-foreground">
      {{ selectedCount }} of {{ totalRows }} row(s) selected.
    </div>
    <div class="flex items-center gap-4 lg:gap-8">
      <div v-if="meta" class="flex items-center gap-2">
        <label class="text-sm font-medium">Rows per page</label>
        <select
          :value="meta.per_page"
          class="h-8 w-[70px] rounded-md border border-input bg-transparent px-3 py-2 text-sm"
          @change="emit('update:perPage', Number(($event.target as HTMLSelectElement).value))"
        >
          <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }}</option>
        </select>
      </div>
      <div v-if="meta" class="flex w-[100px] items-center justify-center text-sm font-medium">
        Page {{ meta.current_page }} of {{ meta.last_page }}
      </div>
      <div v-if="meta && meta.last_page > 1" class="flex items-center gap-2">
        <button
          type="button"
          class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground disabled:opacity-50"
          :disabled="meta.current_page <= 1"
          aria-label="Go to first page"
          @click="emit('page', 1)"
        >
          &laquo;
        </button>
        <button
          type="button"
          class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground disabled:opacity-50"
          :disabled="meta.current_page <= 1"
          aria-label="Go to previous page"
          @click="emit('page', meta.current_page - 1)"
        >
          &lsaquo;
        </button>
        <button
          type="button"
          class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground disabled:opacity-50"
          :disabled="meta.current_page >= meta.last_page"
          aria-label="Go to next page"
          @click="emit('page', meta.current_page + 1)"
        >
          &rsaquo;
        </button>
        <button
          type="button"
          class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground disabled:opacity-50"
          :disabled="meta.current_page >= meta.last_page"
          aria-label="Go to last page"
          @click="emit('page', meta.last_page)"
        >
          &raquo;
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
const perPageOptions = [10, 25, 50, 100]

defineProps<{
  selectedCount: number
  totalRows: number
  meta?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}>()

const emit = defineEmits<{
  (e: 'update:perPage', value: number): void
  (e: 'page', page: number): void
}>()
</script>
