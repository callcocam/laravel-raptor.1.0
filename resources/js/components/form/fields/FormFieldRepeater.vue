<template>
  <div class="space-y-3">
    <div v-if="field.label" class="text-sm font-medium text-foreground">
      {{ field.label }}
    </div>
    <div class="space-y-2">
      <div
        v-for="(row, index) in items"
        :key="rowKey(index)"
        class="flex flex-wrap items-start gap-2 rounded-lg border border-border bg-muted/30 p-3"
      >
        <div
          v-if="field.reorderable !== false"
          class="flex shrink-0 flex-col gap-0.5"
          aria-label="Reordenar"
        >
          <button
            type="button"
            class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground disabled:opacity-40"
            :disabled="index === 0"
            aria-label="Mover para cima"
            @click="moveUp(index)"
          >
            <ChevronUp class="h-4 w-4" />
          </button>
          <button
            type="button"
            class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground disabled:opacity-40"
            :disabled="index === items.length - 1"
            aria-label="Mover para baixo"
            @click="moveDown(index)"
          >
            <ChevronDown class="h-4 w-4" />
          </button>
        </div>
        <div class="min-w-0 flex-1 space-y-2">
          <FieldRenderer
            v-for="innerField in field.fields"
            :key="innerField.name"
            :field="innerField"
            :model-value="getRowValue(row, innerField)"
            @update:model-value="setRowValue(index, innerField, $event)"
          />
        </div>
        <button
          v-if="canRemove"
          type="button"
          class="shrink-0 rounded p-1 text-destructive hover:bg-destructive/10"
          aria-label="Remover item"
          @click="removeRow(index)"
        >
          <Trash2 class="h-4 w-4" />
        </button>
      </div>
    </div>
    <button
      v-if="canAdd"
      type="button"
      class="flex items-center gap-2 rounded-md border border-dashed border-border bg-transparent px-3 py-2 text-sm text-muted-foreground hover:bg-muted hover:text-foreground"
      @click="addRow"
    >
      <Plus class="h-4 w-4" />
      {{ field.addLabel ?? 'Adicionar' }}
    </button>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { ChevronUp, ChevronDown, Plus, Trash2 } from 'lucide-vue-next'
import FieldRenderer from '@raptor/components/form/FieldRenderer.vue'
import { useFormField } from '@raptor/composables/useFormField'
import type { FormRepeater, FormField } from '@raptor/types'

const props = withDefaults(
  defineProps<{
    field: FormRepeater
    modelValue: unknown
  }>(),
  {},
)

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const { emitChange } = useFormField(emit)

const items = computed<Record<string, unknown>[]>(() => {
  const v = props.modelValue
  if (Array.isArray(v)) return v
  return []
})

const minItems = computed(() => props.field.minItems ?? 0)
const maxItems = computed(() => props.field.maxItems ?? null)

const canAdd = computed(() => {
  const max = maxItems.value
  return max === null || items.value.length < max
})

const canRemove = computed(() => {
  const min = minItems.value
  return items.value.length > min
})

function rowKey(index: number): string {
  return `row-${props.field.name}-${index}`
}

function getRowValue(row: Record<string, unknown>, innerField: FormField): unknown {
  return row[innerField.name]
}

function setRowValue(rowIndex: number, innerField: FormField, value: unknown): void {
  const next = items.value.map((row, i) =>
    i === rowIndex ? { ...row, [innerField.name]: value } : row,
  )
  emitChange(next)
}

function addRow(): void {
  if (!canAdd.value) return
  const empty: Record<string, unknown> = {}
  for (const f of props.field.fields) {
    empty[f.name] = null
  }
  emitChange([...items.value, empty])
}

function removeRow(index: number): void {
  if (!canRemove.value) return
  const next = items.value.filter((_, i) => i !== index)
  emitChange(next)
}

function moveUp(index: number): void {
  if (index <= 0) return
  const next = [...items.value]
  ;[next[index - 1], next[index]] = [next[index], next[index - 1]]
  emitChange(next)
}

function moveDown(index: number): void {
  if (index >= items.value.length - 1) return
  const next = [...items.value]
  ;[next[index], next[index + 1]] = [next[index + 1], next[index]]
  emitChange(next)
}
</script>
