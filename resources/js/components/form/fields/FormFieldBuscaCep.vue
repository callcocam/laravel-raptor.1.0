<template>
  <div class="space-y-3">
    <div v-if="field.label" class="text-sm font-medium text-foreground">
      {{ field.label }}
    </div>
    <div :class="formClasses" class="grid grid-cols-1 gap-4">
      <div
        v-for="innerField in addressFields"
        :key="innerField.name"
        :class="getColumnClasses(innerField)"
        class="space-y-2"
      >
        <div v-if="innerField.name === executeOnChangeField" class="flex gap-2">
          <div class="relative min-w-0 flex-1">
            <FieldRenderer
              :field="innerField"
              :model-value="fieldValues[innerField.name]"
              @update:model-value="(v: unknown) => setFieldValue(innerField.name, v)"
            />
            <div
              v-if="isSearching"
              class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
              aria-hidden
            >
              <svg
                class="h-4 w-4 animate-spin"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                />
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
              </svg>
            </div>
          </div>
          <Button
            type="button"
            variant="outline"
            size="default"
            class="shrink-0 self-end"
            :disabled="!canSearch || isSearching"
            @click="handleSearchClick"
          >
            <Search class="mr-2 h-4 w-4" />
            Buscar
          </Button>
        </div>
        <template v-else>
          <FieldRenderer
            :field="innerField"
            :model-value="fieldValues[innerField.name]"
            @update:model-value="(v: unknown) => setFieldValue(innerField.name, v)"
          />
        </template>
      </div>
    </div>
    <p v-if="cepError" class="text-sm text-destructive">
      {{ cepError }}
    </p>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import { Search } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import FieldRenderer from '@raptor/components/form/FieldRenderer.vue'
import { useFormField } from '@raptor/composables/useFormField'
import { useGridLayout } from '@raptor/composables/useGridLayout'
import type { FormField } from '@raptor/types'

const props = defineProps<{
  field: FormField & {
    fields?: FormField[]
    fieldMapping?: Record<string, string>
    executeOnChange?: string
  }
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const { emitChange } = useFormField(emit)
const { getFormClasses, getColumnClasses } = useGridLayout()

const formClasses = computed(() =>
  getFormClasses(
    (props.field.gridColumns as string) ?? '12',
    (props.field.gap as string) ?? '4',
  ),
)

const isSearching = ref(false)
const cepError = ref('')
const fieldValues = ref<Record<string, unknown>>({})

const addressFields = computed(() => props.field.fields ?? [])

const executeOnChangeField = computed(
  () => (props.field.executeOnChange as string) ?? 'zip_code',
)

const canSearch = computed(() => {
  const v = fieldValues.value[executeOnChangeField.value]
  if (v == null || v === '') return false
  const cleaned = String(v).replace(/\D/g, '')
  return cleaned.length === 8
})

watch(
  () => props.modelValue,
  (newValue) => {
    const obj =
      typeof newValue === 'object' && newValue !== null && !Array.isArray(newValue)
        ? (newValue as Record<string, unknown>)
        : {}
    const next: Record<string, unknown> = {}
    for (const f of addressFields.value) {
      next[f.name] = obj[f.name] ?? ''
    }
    fieldValues.value = next
  },
  { immediate: true },
)

function setFieldValue(name: string, value: unknown): void {
  fieldValues.value = { ...fieldValues.value, [name]: value }
  emitChange({ ...fieldValues.value })
}

function handleSearchClick(): void {
  const cep = fieldValues.value[executeOnChangeField.value]
  if (cep == null) return
  const cleaned = String(cep).replace(/\D/g, '')
  if (cleaned.length !== 8) {
    cepError.value = 'CEP deve ter 8 dígitos.'
    return
  }
  searchCep(cleaned)
}

async function searchCep(cep: string): Promise<void> {
  isSearching.value = true
  cepError.value = ''
  try {
    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`)
    const data = (await response.json()) as Record<string, string> & { erro?: boolean }
    if (data.erro) {
      cepError.value = 'CEP não encontrado.'
      return
    }
    const mapping = props.field.fieldMapping ?? {}
    const next = { ...fieldValues.value }
    for (const [apiKey, formKey] of Object.entries(mapping)) {
      next[formKey] = data[apiKey] ?? ''
    }
    fieldValues.value = next
    emitChange(next)
  } catch {
    cepError.value = 'Erro ao buscar CEP. Tente novamente.'
  } finally {
    isSearching.value = false
  }
}
</script>
