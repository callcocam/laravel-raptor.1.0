<!--
  FieldAddonsWrapper - Wrapper for form fields with label+hint, helpText, prepend, append, prefix, suffix.
  Uses InputGroup + InputGroupAddon when there are addons. Provides 'inInputGroup' for child fields.
-->
<template>
  <div class="space-y-2">
    <FormFieldLabelWithHint
      v-if="showLabelWithHint"
      :field="rawField"
      @action-click="(a, e) => $emit('action-click', a, e)"
    />

    <InputGroup v-if="hasAddons">
      <InputGroupAddon v-if="leftAddonContent">
        <template v-for="(item, i) in normalizedLeftAddons" :key="i">
          <InputGroupText v-if="isStringItem(item)">{{ item }}</InputGroupText>
          <ActionRenderer
            v-else
            :action="item as FormAction"
            @click="(e: Event) => $emit('action-click', item, e)"
          />
        </template>
      </InputGroupAddon>

      <slot />

      <InputGroupAddon v-if="rightAddonContent" align="inline-end">
        <template v-for="(item, i) in normalizedRightAddons" :key="i">
          <InputGroupText v-if="isStringItem(item)">{{ item }}</InputGroupText>
          <ActionRenderer
            v-else
            :action="item as FormAction"
            @click="(e: Event) => $emit('action-click', item, e)"
          />
        </template>
      </InputGroupAddon>
    </InputGroup>

    <slot v-else />

    <p v-if="helpText" class="text-sm text-muted-foreground">
      {{ helpText }}
    </p>
  </div>
</template>

<script lang="ts" setup>
import { computed, provide } from 'vue'
import ActionRenderer from '@raptor/components/actions/ActionRenderer.vue'
import FormFieldLabelWithHint from '@raptor/components/form/FormFieldLabelWithHint.vue'
import {
  InputGroup,
  InputGroupAddon,
  InputGroupText,
} from '@/components/ui/input-group'
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

provide('inInputGroup', hasAddons)

const helpText = computed(() => {
  const t = rawField.value?.helpText
  return typeof t === 'string' && t.length > 0 ? t : null
})
</script>
