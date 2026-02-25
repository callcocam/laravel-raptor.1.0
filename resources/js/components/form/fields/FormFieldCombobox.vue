<template>
  <div class="space-y-2">
    <Label v-if="field.label" :for="field.name">
      {{ field.label }}
    </Label>
    <Popover v-model:open="popoverOpen">
      <PopoverAnchor as-child>
        <div class="relative cursor-text" role="combobox" :aria-expanded="popoverOpen"
          :aria-controls="`${field.name}-listbox`" @click="onTriggerClick">
          <Input :id="field.name" :model-value="displayValue" class="w-full pr-8"
            :placeholder="(field.placeholder as string) ?? 'Digite para buscar...'" autocomplete="off" readonly />
          <span v-if="isLoading"
            class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
            aria-hidden>
            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
          </span>
        </div>
      </PopoverAnchor>
      <PopoverContent :id="`${field.name}-listbox`" role="listbox" class="w-[var(--reka-popover-trigger-width)] p-0"
        align="start" @keydown.down.prevent="focusNext" @keydown.up.prevent="focusPrev"
        @keydown.enter.prevent="selectFocused">
        <div class="border-b p-2">
          <input ref="searchInputRef" v-model="searchQuery" type="text"
            class="border-input bg-background h-9 w-full rounded-md border px-3 py-1 text-sm outline-none"
            placeholder="Digite para filtrar..." autocomplete="off" @keydown.down.prevent="focusNext"
            @keydown.up.prevent="focusPrev" @keydown.enter.prevent="selectFocused" />
        </div>
        <ul v-if="searchOptions.length > 0" class="max-h-60 overflow-auto py-1">
          <li v-for="(opt, index) in searchOptions" :id="`${field.name}-option-${opt.value}`" :key="String(opt.value)"
            role="option" :aria-selected="focusedIndex === index" :class="[
              'cursor-pointer px-3 py-2 text-sm outline-none',
              focusedIndex === index
                ? 'bg-accent text-accent-foreground'
                : 'hover:bg-muted/50',
            ]" @click="selectOption(opt)" @mouseenter="focusedIndex = index">
            {{ opt.label }}
          </li>
        </ul>
        <p v-else-if="!isLoading && searchQuery.length > 0" class="px-3 py-4 text-center text-sm text-muted-foreground">
          Nenhum resultado.
        </p>
        <p v-else-if="searchQuery.length === 0" class="px-3 py-4 text-center text-sm text-muted-foreground">
          Digite acima para buscar.
        </p>
      </PopoverContent>
    </Popover>
  </div>
</template>

<script lang="ts" setup>
import { computed, nextTick, ref, watch } from 'vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import {
  Popover,
  PopoverAnchor,
  PopoverContent,
} from '@/components/ui/popover'
import type { FormField } from '@raptor/types'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const page = usePage()
const searchQuery = ref('')
/** Opções vêm do servidor; quando o form é atualizado pelo reload com ?combobox&q, props.field.options muda e a lista reage */
const searchOptions = computed(() => (props.field.options ?? []) as Array<{ value: string | number; label: string }>)
const isLoading = ref(false)
const popoverOpen = ref(false)
const focusedIndex = ref(0)
const searchInputRef = ref<HTMLInputElement | null>(null)
/** Label do item selecionado na busca (não está em field.options); usado para exibir no trigger */
const selectedLabelFromSearch = ref('')

let debounceTimer: ReturnType<typeof setTimeout> | null = null
const DEBOUNCE_MS = 300

const selectedLabelFromOptions = computed(() => {
  if (props.modelValue == null || props.modelValue === '') return ''
  const opts = props.field.options ?? []
  const found = opts.find((o: { value: string | number }) => String(o.value) === String(props.modelValue))
  return found ? String(found.label) : ''
})

const displayValue = computed(() => {
  if (popoverOpen.value) return searchQuery.value
  return selectedLabelFromSearch.value || selectedLabelFromOptions.value || ''
})


function fetchOptions(q: string) {
  isLoading.value = true
  router.get(
    page.url,
    { combobox: props.field.name, q },
    {
      only: ['form.fields'],
      preserveState: true,
      preserveScroll: true, 
      onSuccess: (response: any) => {
        // props.field.options = response.form.fields[props.field.name].options
        isLoading.value = false
      },
      onError: () => {
        isLoading.value = false
      },
    },
  )
}

function onTriggerClick() {
  popoverOpen.value = true
  searchQuery.value = ''
  nextTick(() => {
    searchInputRef.value?.focus()
  })
}

function selectOption(opt: { value: string | number; label: string }) {
  selectedLabelFromSearch.value = opt.label
  emit('update:modelValue', opt.value)
  popoverOpen.value = false
}

function focusNext() {
  if (focusedIndex.value < searchOptions.value.length - 1) {
    focusedIndex.value += 1
  }
}

function focusPrev() {
  if (focusedIndex.value > 0) {
    focusedIndex.value -= 1
  }
}

function selectFocused() {
  const opt = searchOptions.value[focusedIndex.value]
  if (opt) selectOption(opt)
}

watch(
  () => props.modelValue,
  (val) => {
    if (val == null || val === '') {
      searchQuery.value = ''
      selectedLabelFromSearch.value = ''
      return
    }
    const labelFromOptions = selectedLabelFromOptions.value
    if (labelFromOptions) {
      selectedLabelFromSearch.value = labelFromOptions
    }
    if (labelFromOptions && !popoverOpen.value) searchQuery.value = labelFromOptions
  },
  { immediate: true },
)

watch(searchQuery, (q) => {
  if (!popoverOpen.value) return
  if (debounceTimer) clearTimeout(debounceTimer)
  if (q.trim().length === 0) return
  debounceTimer = setTimeout(() => {
    fetchOptions(q)
    debounceTimer = null
  }, DEBOUNCE_MS)
})

watch(
  () => searchOptions.value.length,
  () => {
    focusedIndex.value = 0
  },
)
</script>
