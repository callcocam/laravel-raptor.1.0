<template>
  <div class="flex items-center justify-between px-2 py-2">
    <div class="flex-1 text-sm text-muted-foreground">
      {{ selectedCount }} of {{ totalRows }} row(s) selected.
    </div>
    <div class="flex items-center gap-4 lg:gap-8">
      <div v-if="meta" class="flex items-center gap-2">
        <Label class="text-sm font-medium">Rows per page</Label>
        <Select
          :model-value="String(meta.per_page)"
          @update:model-value="emit('update:perPage', Number($event))"
        >
          <SelectTrigger class="h-8 w-[70px]">
            <SelectValue :placeholder="String(meta.per_page)" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="n in perPageOptions" :key="n" :value="String(n)">
              {{ n }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>
      <div v-if="meta" class="flex w-[100px] items-center justify-center text-sm font-medium">
        Page {{ meta.current_page }} of {{ meta.last_page }}
      </div>
      <div v-if="meta && meta.last_page > 1" class="flex items-center gap-2">
        <Button
          variant="outline"
          size="icon"
          class="h-8 w-8"
          :disabled="meta.current_page <= 1"
          aria-label="Go to first page"
          @click="emit('page', 1)"
        >
          <ChevronsLeft class="h-4 w-4" />
        </Button>
        <Button
          variant="outline"
          size="icon"
          class="h-8 w-8"
          :disabled="meta.current_page <= 1"
          aria-label="Go to previous page"
          @click="emit('page', meta.current_page - 1)"
        >
          <ChevronLeft class="h-4 w-4" />
        </Button>
        <Button
          variant="outline"
          size="icon"
          class="h-8 w-8"
          :disabled="meta.current_page >= meta.last_page"
          aria-label="Go to next page"
          @click="emit('page', meta.current_page + 1)"
        >
          <ChevronRight class="h-4 w-4" />
        </Button>
        <Button
          variant="outline"
          size="icon"
          class="h-8 w-8"
          :disabled="meta.current_page >= meta.last_page"
          aria-label="Go to last page"
          @click="emit('page', meta.last_page)"
        >
          <ChevronsRight class="h-4 w-4" />
        </Button>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

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
