<!--
  FormFieldLabelWithHint - Label + hint discreto (texto ou actions) para usar em todos os campos.
  Usado pelo FieldAddonsWrapper. Ações de hint usam botão nativo inline (alinhamento estável) e
  executam executeUrl no clique quando existir (CallbackAction no backend).
-->
<template>
  <div
    v-if="hasContent"
    class="flex w-full flex-nowrap items-center justify-between gap-2"
  >
    <Label
      v-if="label"
      :for="forId ?? undefined"
      class="min-w-0 shrink truncate"
    >
      {{ label }}
    </Label>
    <span
      v-if="hintContent"
      class="flex shrink-0 flex-nowrap items-center gap-1 text-xs text-muted-foreground"
    >
      <template v-for="(item, i) in normalizedHints" :key="i">
        <span v-if="isStringItem(item)" class="whitespace-nowrap">{{ item }}</span>
        <button
          v-else
          type="button"
          class="cursor-pointer whitespace-nowrap rounded border-0 bg-transparent p-0 text-left text-xs text-muted-foreground underline-offset-2 hover:underline focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50"
          :disabled="(item as FormAction).disabled"
          :title="(item as FormAction).tooltip ?? undefined"
          @click="onHintActionClick(item as FormAction, $event)"
        >
          {{ (item as FormAction).label }}
        </button>
      </template>
    </span>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Label } from '@/components/ui/label'
import type { FormAction } from '@raptor/types'

const props = withDefaults(
  defineProps<{
    label?: string | null
    hint?: string | string[] | FormAction[] | null
    forId?: string | null
  }>(),
  {
    label: null,
    hint: null,
    forId: null,
  },
)

const emit = defineEmits<{
  (e: 'action-click', action: FormAction, event: Event): void
}>()

function onHintActionClick(action: FormAction, event: Event) {
  emit('action-click', action, event)
  if (action.executeUrl) {
    router.post(action.executeUrl)
  }
}

function isStringItem(value: unknown): value is string {
  return typeof value === 'string'
}

function normalizeHint(
  value:
    | string
    | string[]
    | FormAction[]
    | null
    | undefined,
): Array<string | FormAction> {
  if (value == null) return []
  if (typeof value === 'string') return value ? [value] : []
  if (Array.isArray(value)) {
    return value.filter(
      (v): v is string | FormAction =>
        typeof v === 'string' ||
        (v != null &&
          typeof v === 'object' &&
          typeof (v as FormAction).label === 'string'),
    )
  }
  return []
}

const normalizedHints = computed(() =>
  normalizeHint(props.hint as string | string[] | FormAction[] | null | undefined),
)

const hintContent = computed(() => normalizedHints.value.length > 0)

const hasContent = computed(
  () => (props.label && props.label.length > 0) || hintContent.value,
)
</script>
