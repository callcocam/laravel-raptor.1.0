<template>
  <div class="flex items-center gap-2 relative">
    <DynamicIcon v-if="column.icon" :name="column.icon" class="h-4 w-4 shrink-0" />
    <textarea
      ref="textareaRef"
      v-model="editValue"
      :disabled="isSaving"
      :rows="column.rows || 3"
      :placeholder="column.placeholder || column.label"
      class="flex-1 px-3 py-2 border rounded-md bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white text-sm resize-none"
      @blur="saveEdit"
    />
    <Loader2Icon v-if="isSaving" :size="16" class="animate-spin text-gray-500" />

    <!-- Error Message -->
    <div v-if="error" class="text-red-500 text-xs absolute -bottom-5 left-0 whitespace-nowrap">
      {{ error }}
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Loader2Icon } from 'lucide-vue-next'
import DynamicIcon from '@raptor/components/ui/DynamicIcon.vue'

interface Column {
  name: string
  label?: string
  rows?: number
  icon?: string
  placeholder?: string
  [key: string]: unknown
}

const props = defineProps<{
  record: Record<string, unknown>
  column: Column
}>()

const isSaving = ref(false)
const error = ref('')
const editValue = ref('')
const originalValue = ref('')

const cellValue = computed(() => {
  if (props.column.name in props.record) {
    const value = props.record[props.column.name]
    return value ? String(value) : ''
  }

  const keys = props.column.name.split('.')
  let value: unknown = props.record

  for (const key of keys) {
    if (value && typeof value === 'object' && key in value) {
      value = (value as Record<string, unknown>)[key]
    } else {
      value = null
      break
    }
  }

  return value ? String(value) : ''
})

watch(
  () => cellValue.value,
  (newVal) => {
    editValue.value = newVal
    originalValue.value = newVal
  },
  { immediate: true }
)

const resolveUpdateUrl = (): string => {
  const routeName = window.location.pathname.split('/')[1]
  const recordId = props.record.id
  return `/${routeName}/${recordId}/update-field`
}

const saveEdit = () => {
  if (editValue.value === originalValue.value) {
    return
  }

  if (isSaving.value) {
    return
  }

  isSaving.value = true
  error.value = ''

  const recordId = props.record.id
  if (!recordId) {
    error.value = 'ID não encontrado'
    isSaving.value = false
    return
  }

  router.post(
    resolveUpdateUrl(),
    {
      field: props.column.name,
      value: editValue.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        originalValue.value = editValue.value
        isSaving.value = false
      },
      onError: (errors) => {
        error.value = errors.value?.toString() || 'Erro ao atualizar'
        isSaving.value = false
      },
    },
  )
}
</script>
