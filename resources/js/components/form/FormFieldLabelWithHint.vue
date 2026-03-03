<!--
  FormFieldLabelWithHint - Label + hint discreto (texto ou actions) para usar em todos os campos.
  Recebe o field diretamente e decide internamente se mostra label, hint ou ambos.
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
        <ActionRenderer
          v-else
          :action="item"
          @click="(e: Event) => onHintActionClick(item as FormAction, e)"
        />
      </template>
    </span>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Label } from '@/components/ui/label'
import ActionRenderer from '@raptor/components/actions/ActionRenderer.vue'
import type { FormField, FormAction } from '@raptor/types'

const props = defineProps<{
  field: FormField | null
}>()

const emit = defineEmits<{
  (e: 'action-click', action: FormAction, event: Event): void
}>()

const label = computed(() => props.field?.label ?? null)
const forId = computed(() =>
  typeof props.field?.name === 'string' ? props.field.name : null,
)

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
  value: string | string[] | FormAction[] | null | undefined,
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
  normalizeHint(props.field?.hint as string | string[] | FormAction[] | null | undefined),
)

const hintContent = computed(() => normalizedHints.value.length > 0)

const hasContent = computed(
  () => (label.value && label.value.length > 0) || hintContent.value,
)
</script>
