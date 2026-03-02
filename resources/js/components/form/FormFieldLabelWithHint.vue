<!--
  FormFieldLabelWithHint - Label + hint discreto (texto ou actions) para usar em todos os campos.
  Usado pelo FieldAddonsWrapper para centralizar label e hint; assim os field components não precisam repetir.
-->
<template>
  <div
    v-if="hasContent"
    class="flex items-center gap-2"
  >
    <Label
      v-if="label"
      :for="forId ?? undefined"
    >
      {{ label }}
    </Label>
    <span
      v-if="hintContent"
      class="text-xs text-muted-foreground"
    >
      <template v-for="(item, i) in normalizedHints" :key="i">
        <span v-if="isStringItem(item)">{{ item }}</span>
        <ActionRenderer
          v-else
          :action="item as FormAction"
          class="inline-flex"
          @click="(e: Event) => $emit('action-click', item, e)"
        />
      </template>
    </span>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { Label } from '@/components/ui/label'
import ActionRenderer from '@raptor/components/actions/ActionRenderer.vue'
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

defineEmits<{
  (e: 'action-click', action: FormAction, event: Event): void
}>()

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
