<!--
  FieldAddonsWrapper - Wrapper for form fields with label+hint, helpText, prepend, append, prefix, suffix.
  Usa FormFieldLabelWithHint para label+hint em todos os campos; os field components podem esconder a label via inject.
-->
<template>
  <div class="space-y-2">
    <FormFieldLabelWithHint
      v-if="showLabelWithHint"
      :label="labelForField"
      :hint="rawField.hint"
      :for-id="forIdForField"
      @action-click="(a, e) => $emit('action-click', a, e)"
    />
    <div :class="hasAddons ? 'flex items-end rounded-md shadow-sm' : ''">
      <div
        v-if="leftAddonContent"
        class="inline-flex h-9 shrink-0 items-center gap-1 px-3 rounded-l-md border border-r-0 border-input bg-muted text-muted-foreground text-sm"
      >
        <template v-for="(item, i) in normalizedLeftAddons" :key="i">
          <span v-if="isStringItem(item)" class="inline">{{ item }}</span>
          <ActionRenderer
            v-else
            :action="item as FormAction"
            class="inline-flex"
            @click="(e: Event) => $emit('action-click', item, e)"
          />
        </template>
      </div>

      <div :class="hasAddons ? (leftAddonContent ? 'rounded-none flex-1 min-w-0' : 'rounded-l-md flex-1 min-w-0') : ''">
        <slot />
      </div>

      <div
        v-if="rightAddonContent"
        class="inline-flex h-9 shrink-0 items-center gap-1 px-3 rounded-r-md border border-l-0 border-input bg-muted text-muted-foreground text-sm -ml-2"
      >
        <template v-for="(item, i) in normalizedRightAddons" :key="i">
          <span v-if="isStringItem(item)" class="inline">{{ item }}</span>
          <ActionRenderer
            v-else
            :action="item as FormAction"
            class="inline-flex"
            @click="(e: Event) => $emit('action-click', item, e)"
          />
        </template>
      </div>
    </div>

    <p
      v-if="helpText"
      class="text-sm text-muted-foreground"
    >
      {{ helpText }}
    </p>
  </div>
</template>

<script lang="ts" setup>
import { computed, provide } from 'vue'
import ActionRenderer from '@raptor/components/actions/ActionRenderer.vue'
import FormFieldLabelWithHint from '@raptor/components/form/FormFieldLabelWithHint.vue'
import type {
  FormField,
  FormFieldOrSection,
  FormAction,
} from '@raptor/types'

const props = defineProps<{
  field: FormFieldOrSection
}>()

defineEmits<{
  (e: 'action-click', action: FormAction, event: Event): void
}>()

const rawField = computed(() => props.field as FormField)

const isSectionOrRepeater = computed(() => {
  const t = (props.field as { type?: string }).type
  return t === 'section' || t === 'repeater'
})

const hasHintContent = computed(() => {
  const h = rawField.value?.hint
  if (h == null) return false
  if (typeof h === 'string') return h.length > 0
  return Array.isArray(h) && h.length > 0
})

const showLabelWithHint = computed(
  () =>
    !isSectionOrRepeater.value &&
    (!!rawField.value?.label || hasHintContent.value),
)

const labelForField = computed(() => rawField.value?.label ?? null)

const forIdForField = computed(() =>
  typeof rawField.value?.name === 'string' ? rawField.value.name : null,
)

provide('fieldLabelRenderedByWrapper', true)

function isActionLike(value: unknown): value is FormAction {
  if (value == null || typeof value !== 'object') return false
  const o = value as Record<string, unknown>
  return typeof (o.label ?? o.name) === 'string'
}

function isStringItem(value: unknown): value is string {
  return typeof value === 'string'
}

function normalizeAddon(
  value: string | FormAction | FormAction[] | null | undefined,
): Array<string | FormAction> {
  if (value == null) return []
  if (typeof value === 'string') return value ? [value] : []
  if (Array.isArray(value)) {
    return value.filter(
      (v: unknown): v is string | FormAction =>
        typeof v === 'string' || isActionLike(v as FormAction),
    )
  }
  return isActionLike(value) ? [value] : []
}

const normalizedLeftAddons = computed(() => {
  const left = rawField.value?.prepend ?? rawField.value?.prefix
  return normalizeAddon(
    left as string | FormAction | FormAction[] | null | undefined,
  )
})

const normalizedRightAddons = computed(() => {
  const right = rawField.value?.append ?? rawField.value?.suffix
  return normalizeAddon(
    right as string | FormAction | FormAction[] | null | undefined,
  )
})

const leftAddonContent = computed(
  () => normalizedLeftAddons.value.length > 0,
)
const rightAddonContent = computed(
  () => normalizedRightAddons.value.length > 0,
)

const hasAddons = computed(
  () => leftAddonContent.value || rightAddonContent.value,
)

const helpText = computed(() => {
  const t = rawField.value?.helpText
  return typeof t === 'string' && t.length > 0 ? t : null
})
</script>
