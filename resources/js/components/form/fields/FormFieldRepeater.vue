<template>
  <Collapsible
    v-if="field.collapsible !== false"
    :default-open="field.defaultOpen !== false"
    class="group/repeater-section overflow-hidden rounded-lg border border-border"
  >
    <CollapsibleTrigger
      as-child
      class="flex w-full cursor-pointer items-center justify-between gap-2 rounded-t-lg border-b border-border bg-muted/30 px-3 py-3 outline-none focus-visible:ring-2 focus-visible:ring-ring"
    >
      <button type="button" class="flex flex-1 items-center gap-2 text-left">
        <ChevronDown
          class="h-4 w-4 shrink-0 text-muted-foreground transition-transform duration-200 group-data-[state=open]/repeater-section:rotate-180"
        />
        <span class="text-sm font-medium text-foreground">
          {{ field.label ?? 'Itens' }}
        </span>
        <span v-if="items.length > 0" class="text-xs text-muted-foreground">
          ({{ items.length }} {{ items.length === 1 ? 'item' : 'itens' }})
        </span>
      </button>
    </CollapsibleTrigger>
    <CollapsibleContent>
      <div class="space-y-3 p-3">
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
            <div :class="formClasses" class="min-w-0 flex-1">
              <div
                v-for="innerField in field.fields"
                :key="innerField.name"
                :class="getColumnClasses(innerField)"
                :style="(getColumnStyles(innerField) as Record<string, string | number>)"
                class="space-y-2"
              >
                <FieldRenderer
                  :field="innerField"
                  :model-value="getRowValue(row, innerField)"
                  @update:model-value="setRowValue(index, innerField, $event)"
                />
              </div>
            </div>
            <div class="flex shrink-0 items-center gap-0.5">
              <template v-for="itemAction in (field.itemActions ?? [])" :key="itemAction.name">
                <button
                  v-if="!itemAction.disabled"
                  type="button"
                  class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
                  :aria-label="itemAction.label"
                  :title="(itemAction.tooltip as string) ?? itemAction.label"
                  @click="runItemAction(itemAction, index, row)"
                >
                  <DynamicIcon v-if="itemAction.icon" :name="itemAction.icon" class="h-4 w-4" />
                  <span v-else class="text-xs">{{ itemAction.label }}</span>
                </button>
              </template>
              <button
                type="button"
                class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
                aria-label="Copiar item"
                @click="copyRow(index)"
              >
                <Copy class="h-4 w-4" />
              </button>
              <button
                v-if="canRemove"
                type="button"
                class="rounded p-1 text-destructive hover:bg-destructive/10"
                aria-label="Remover item"
                @click="removeRow(index)"
              >
                <Trash2 class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
        <div v-if="canAdd" class="flex w-full justify-center">
          <button
            type="button"
            class="flex items-center gap-2 rounded-md border border-dashed border-border bg-transparent px-3 py-2 text-sm text-muted-foreground hover:bg-muted hover:text-foreground"
            @click="addRow"
          >
            <Plus class="h-4 w-4" />
            {{ field.addLabel ?? 'Adicionar' }}
          </button>
        </div>
      </div>
    </CollapsibleContent>
  </Collapsible>
  <div v-else class="space-y-3">
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
        <div :class="formClasses" class="min-w-0 flex-1">
          <div
            v-for="innerField in field.fields"
            :key="innerField.name"
            :class="getColumnClasses(innerField)"
            :style="(getColumnStyles(innerField) as Record<string, string | number>)"
            class="space-y-2"
          >
            <FieldRenderer
              :field="innerField"
              :model-value="getRowValue(row, innerField)"
              @update:model-value="setRowValue(index, innerField, $event)"
            />
          </div>
        </div>
        <div class="flex shrink-0 items-center gap-0.5">
          <template v-for="itemAction in (field.itemActions ?? [])" :key="itemAction.name">
            <button
              v-if="!itemAction.disabled"
              type="button"
              class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
              :aria-label="itemAction.label"
              :title="(itemAction.tooltip as string) ?? itemAction.label"
              @click="runItemAction(itemAction, index, row)"
            >
              <DynamicIcon v-if="itemAction.icon" :name="itemAction.icon" class="h-4 w-4" />
              <span v-else class="text-xs">{{ itemAction.label }}</span>
            </button>
          </template>
          <button
            type="button"
            class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
            aria-label="Copiar item"
            @click="copyRow(index)"
          >
            <Copy class="h-4 w-4" />
          </button>
          <button
            v-if="canRemove"
            type="button"
            class="rounded p-1 text-destructive hover:bg-destructive/10"
            aria-label="Remover item"
            @click="removeRow(index)"
          >
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>
    <div v-if="canAdd" class="flex w-full justify-center">
      <button
        type="button"
        class="flex items-center gap-2 rounded-md border border-dashed border-border bg-transparent px-3 py-2 text-sm text-muted-foreground hover:bg-muted hover:text-foreground"
        @click="addRow"
      >
        <Plus class="h-4 w-4" />
        {{ field.addLabel ?? 'Adicionar' }}
      </button>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, inject, onMounted, watch } from 'vue'
import { ChevronUp, ChevronDown, Plus, Trash2, Copy } from 'lucide-vue-next'
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible'
import { router } from '@inertiajs/vue3'
import DynamicIcon from '@raptor/components/ui/DynamicIcon.vue'
import FieldRenderer from '@raptor/components/form/FieldRenderer.vue'
import { useFormField } from '@raptor/composables/useFormField'
import { useGridLayout } from '@raptor/composables/useGridLayout'
import {
  evaluateRepeaterFormula,
  parseNumberFromField,
} from '@raptor/utils/evaluateRepeaterFormula'
import type {
  FormRepeater,
  FormRepeaterItemAction,
  FormRepeaterRowCalculation,
  FormRepeaterSummaryCalculation,
  FormField,
} from '@raptor/types'

const props = withDefaults(
  defineProps<{
    field: FormRepeater
    modelValue: unknown
  }>(),
  {},
)

const { getFormClasses, getColumnClasses, getColumnStyles } = useGridLayout()
const formClasses = computed(() =>
  getFormClasses(
    props.field.gridColumns ?? '12',
    props.field.gap ?? '4',
  ),
)

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const { emitChange } = useFormField(emit)

const setRepeaterSummary = inject<(fieldName: string, value: unknown) => void>(
  'setRepeaterSummary',
  () => {},
)

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

function applyRowCalculations(row: Record<string, unknown>): Record<string, unknown> {
  const calcs = props.field.rowCalculations ?? []
  if (calcs.length === 0) return { ...row }
  const out = { ...row }
  for (const calc of calcs as FormRepeaterRowCalculation[]) {
    const value = evaluateRepeaterFormula(calc.formula, out)
    out[calc.targetField] = value
  }
  return out
}

function runSummaryCalculations(itemsList: Record<string, unknown>[]): void {
  const summaries = props.field.summaryCalculations ?? []
  for (const s of summaries as FormRepeaterSummaryCalculation[]) {
    const values = itemsList.map((row) => parseNumberFromField(row[s.sourceField]))
    let result: number
    if (s.operation === 'sum') result = values.reduce((a, b) => a + b, 0)
    else if (s.operation === 'avg') result = values.length ? values.reduce((a, b) => a + b, 0) / values.length : 0
    else result = values.length
    setRepeaterSummary(s.targetField, result)
  }
}

function emitWithCalculations(next: Record<string, unknown>[]): void {
  const withRowCalcs = next.map((row) => applyRowCalculations(row))
  emitChange(withRowCalcs)
  runSummaryCalculations(withRowCalcs)
}

function setRowValue(rowIndex: number, innerField: FormField, value: unknown): void {
  const next = items.value.map((row, i) =>
    i === rowIndex ? { ...row, [innerField.name]: value } : row,
  )
  emitWithCalculations(next)
}

function addRow(): void {
  if (!canAdd.value) return
  const empty: Record<string, unknown> = {}
  for (const f of props.field.fields) {
    empty[f.name] = null
  }
  emitWithCalculations([...items.value, empty])
}

function copyRow(index: number): void {
  if (!canAdd.value) return
  const row = items.value[index]
  const copy = JSON.parse(JSON.stringify(row)) as Record<string, unknown>
  const next = [...items.value]
  next.splice(index + 1, 0, copy)
  emitWithCalculations(next)
}

function runItemAction(
  action: FormRepeaterItemAction,
  index: number,
  row: Record<string, unknown>,
): void {
  const url = action.executeUrl
  if (!url) return
  router.post(url, { index, item: row })
}

function removeRow(index: number): void {
  if (!canRemove.value) return
  const next = items.value.filter((_, i) => i !== index)
  emitWithCalculations(next)
}

function moveUp(index: number): void {
  if (index <= 0) return
  const next = [...items.value]
  ;[next[index - 1], next[index]] = [next[index], next[index - 1]]
  emitWithCalculations(next)
}

function moveDown(index: number): void {
  if (index >= items.value.length - 1) return
  const next = [...items.value]
  ;[next[index], next[index + 1]] = [next[index + 1], next[index]]
  emitWithCalculations(next)
}

watch(
  items,
  (list) => {
    if ((props.field.summaryCalculations?.length ?? 0) > 0) {
      runSummaryCalculations(list)
    }
  },
  { immediate: true, deep: true },
)

onMounted(() => {
  if (
    (props.field.rowCalculations?.length ?? 0) > 0 &&
    items.value.length > 0
  ) {
    emitWithCalculations(items.value)
  }
})
</script>
