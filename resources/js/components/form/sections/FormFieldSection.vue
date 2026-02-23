<template>
  <Card class="overflow-hidden">
    <Collapsible
      v-if="section.collapsible"
      :default-open="section.defaultOpen !== false"
      class="group/collapsible"
    >
      <CardHeader class="py-4">
        <CollapsibleTrigger
          as-child
          class="flex w-full cursor-pointer items-center justify-between gap-2 rounded-md outline-none focus-visible:ring-2 focus-visible:ring-ring"
        >
          <div class="flex flex-1 items-center justify-between gap-2">
            <CardTitle v-if="section.label" class="text-base">
              {{ section.label }}
            </CardTitle>
            <span v-else class="text-base font-semibold">Seção</span>
            <ChevronDown
              class="h-4 w-4 shrink-0 transition-transform duration-200 group-data-[state=open]/collapsible:rotate-180"
            />
          </div>
        </CollapsibleTrigger>
      </CardHeader>
      <CollapsibleContent>
        <CardContent class="pt-0">
          <div :class="formClasses" class="space-y-2">
            <div
              v-for="innerField in section.fields"
              :key="getFieldValueKey(section, innerField)"
              :class="getColumnClasses(innerField)"
              :style="(getColumnStyles(innerField) as Record<string, string | number>)"
              class="space-y-2"
            >
              <FieldRenderer
                :field="innerField"
                :model-value="getFieldValue(section, innerField)"
                @update:model-value="setFieldValue(section, innerField, $event)"
              />
            </div>
          </div>
        </CardContent>
      </CollapsibleContent>
    </Collapsible>
    <template v-else>
      <CardHeader v-if="section.label">
        <CardTitle class="text-base">{{ section.label }}</CardTitle>
      </CardHeader>
      <CardContent :class="section.label ? '' : 'pt-6'">
        <div :class="formClasses" class="space-y-2">
          <div
            v-for="innerField in section.fields"
            :key="getFieldValueKey(section, innerField)"
            :class="getColumnClasses(innerField)"
            :style="(getColumnStyles(innerField) as Record<string, string | number>)"
            class="space-y-2"
          >
            <FieldRenderer
              :field="innerField"
              :model-value="getFieldValue(section, innerField)"
              @update:model-value="setFieldValue(section, innerField, $event)"
            />
          </div>
        </div>
      </CardContent>
    </template>
  </Card>
</template>

<script lang="ts" setup>
import { computed, inject } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible'
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { useGridLayout } from '@raptor/composables/useGridLayout'
import FieldRenderer from '@raptor/components/form/FieldRenderer.vue'
import type { FormSection, FormField } from '@raptor/types'

const props = defineProps<{
  field: FormSection
}>()

const section = props.field

const { getFormClasses } = useGridLayout()
const formClassesInjected = inject<{ value: string } | string>('formClasses', '')
const formClasses = computed(() => {
  const raw =
    typeof formClassesInjected === 'object' &&
    formClassesInjected &&
    'value' in formClassesInjected
      ? formClassesInjected.value
      : String(formClassesInjected ?? '')
  return (raw && String(raw).trim()) ? raw : getFormClasses('12', '4')
})
const getColumnClasses = inject<(col: FormField) => string>(
  'getColumnClasses',
  (col: FormField) => '',
)
const getColumnStyles = inject<(col: FormField) => Record<string, unknown>>(
  'getColumnStyles',
  (col: FormField) => ({}),
)
const getFieldValueKey = inject<(section: FormSection, field: FormField) => string>(
  'getFieldValueKey',
  (section: FormSection, field: FormField) => '',
)
const getFieldValue = inject<(section: FormSection, field: FormField) => unknown>(
  'getFieldValue',
  (section: FormSection, field: FormField) => undefined,
)
const setFieldValue = inject<
  (section: FormSection, field: FormField, value: unknown) => void
>('setFieldValue', (section: FormSection, field: FormField, value: unknown) => {})
</script>
