<template>
  <div class="flex items-center gap-2 relative">
    <DynamicIcon v-if="column.icon" :name="column.icon" class="h-4 w-4 shrink-0" />
    <Select v-model="editValue" :disabled="isSaving" @update:model-value="saveEdit">
      <SelectTrigger class="h-8 flex-1 text-sm">
        <SelectValue />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="">
          -- Selecione --
        </SelectItem>
        <SelectItem
          v-for="(label, key) in column.options"
          :key="key"
          :value="String(key)"
        >
          {{ label }}
        </SelectItem>
      </SelectContent>
    </Select>
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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import DynamicIcon from '@raptor/components/ui/DynamicIcon.vue'

interface Column {
  name: string
  label?: string
  options?: Record<string, string>
  icon?: string
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

const saveEdit = (value?: unknown) => {
  const newValue = value !== undefined && value !== null ? String(value) : editValue.value

  if (newValue === originalValue.value) {
    return
  }

  if (isSaving.value) {
    return
  }

  editValue.value = newValue
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
      value: newValue,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        originalValue.value = newValue
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
