<template>
  <form
    class="space-y-6"
    @submit.prevent="handleSubmit"
  >
    <div
      :class="fieldsGridClass"
      class="space-y-2"
    >
      <div
        v-for="field in fields"
        :key="field.name"
        :class="fieldSpanClass(field)"
        class="space-y-2"
      >
        <FieldRenderer
          :field="field"
          :model-value="formData[field.name]"
          @update:model-value="setValue(field.name, $event)"
        />
      </div>
    </div>
    <div
      v-if="hasFooterActions"
      class="flex flex-wrap justify-end gap-2"
    >
      <template
        v-for="action in footerActions"
        :key="action.name"
      >
        <Button
          v-if="action.type === 'submit'"
          type="submit"
          :variant="(action.variant as 'default' | 'outline' | 'ghost') ?? 'default'"
          :disabled="isSubmitting || action.disabled"
        >
          {{ isSubmitting ? 'Salvando...' : action.label }}
        </Button>
        <Button
          v-else-if="action.type === 'url' || action.type === 'cancel'"
          type="button"
          :variant="(action.variant as 'default' | 'outline' | 'ghost') ?? 'outline'"
          :disabled="action.disabled"
          @click="handleFooterAction(action)"
        >
          {{ action.label }}
        </Button>
      </template>
    </div>
    <div
      v-else-if="submitUrl"
      class="flex justify-end gap-2"
    >
      <Button
        type="submit"
        :disabled="isSubmitting"
      >
        {{ isSubmitting ? 'Salvando...' : 'Salvar' }}
      </Button>
    </div>
  </form>
</template>

<script lang="ts" setup>
import { computed, reactive, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import FieldRenderer from '@raptor/components/form/FieldRenderer.vue'
import type { FormField, FormActionPayload, FormGridLayout } from '@raptor/types'

const props = withDefaults(
  defineProps<{
    fields: FormField[]
    values?: Record<string, unknown>
    submitUrl?: string | null
    submitMethod?: string
    footerActions?: FormActionPayload[]
    gridLayout?: FormGridLayout | null
  }>(),
  {
    values: () => ({}),
    submitUrl: null,
    submitMethod: 'post',
    footerActions: () => [],
    gridLayout: null,
  }
)

const formData = reactive<Record<string, unknown>>({ ...props.values })
const isSubmitting = ref(false)

const hasFooterActions = computed(() => (props.footerActions?.length ?? 0) > 0)

const gridColsMap: Record<string, string> = {
  '1': 'grid-cols-1',
  '2': 'grid-cols-2',
  '3': 'grid-cols-3',
  '4': 'grid-cols-4',
  '5': 'grid-cols-5',
  '6': 'grid-cols-6',
}
const gapMap: Record<string, string> = {
  '1': 'gap-1',
  '2': 'gap-2',
  '3': 'gap-3',
  '4': 'gap-4',
  '6': 'gap-6',
  '8': 'gap-8',
}
const colSpanMap: Record<string, string> = {
  '1': 'col-span-1',
  '2': 'col-span-2',
  '3': 'col-span-3',
  '4': 'col-span-4',
  full: 'col-span-full',
}

const fieldsGridClass = computed(() => {
  const layout = props.gridLayout
  if (!layout) return ''
  const parts = ['grid']
  if (layout.gridColumns && gridColsMap[layout.gridColumns]) {
    parts.push(gridColsMap[layout.gridColumns])
  }
  if (layout.gap && gapMap[layout.gap]) {
    parts.push(gapMap[layout.gap])
  }
  return parts.length > 1 ? parts.join(' ') : ''
})

function fieldSpanClass(field: FormField): string {
  const span = (field.columnSpan as string | undefined) ?? ''
  return colSpanMap[span] ?? ''
}

watch(
  () => props.values,
  (newVal) => {
    Object.keys(newVal ?? {}).forEach((key) => {
      formData[key] = newVal![key]
    })
  },
  { deep: true }
)

function setValue(name: string, value: unknown) {
  formData[name] = value
}

function handleFooterAction(action: FormActionPayload) {
  if ((action.type === 'url' || action.type === 'cancel') && action.url) {
    if (action.inertia) {
      router.visit(action.url)
    } else {
      window.location.href = action.url
    }
  }
}

function handleSubmit() {
  if (!props.submitUrl) {
    return
  }

  isSubmitting.value = true
  const method = (props.submitMethod ?? 'post').toLowerCase()

  const onFinish = () => {
    isSubmitting.value = false
  }

  if (method === 'put' || method === 'patch') {
    router.put(props.submitUrl, formData as Record<string, string>, { onFinish })
  } else {
    router.post(props.submitUrl, formData as Record<string, string>, { onFinish })
  }
}
</script>
