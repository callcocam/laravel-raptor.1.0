<template>
  <div class="space-y-2">
    <Label
      v-if="field.label"
      :for="field.name"
    >
      {{ field.label }}
    </Label>
    <!-- Modo busca ao digitar: combobox com popover -->
    <template v-if="isSearchMode">
      <Popover v-model:open="popoverOpen">
        <PopoverAnchor as-child>
          <div
            class="relative cursor-text"
            role="combobox"
            :aria-expanded="popoverOpen"
            :aria-controls="`${field.name}-listbox`"
            @click="onTriggerClick"
          >
            <Input
              :id="field.name"
              ref="inputRef"
              :model-value="searchQuery"
              class="w-full pr-8"
              :placeholder="(field.placeholder as string) ?? 'Digite para buscar...'"
              autocomplete="off"
              aria-autocomplete="list"
              :aria-activedescendant="activeId"
              readonly
              @focus="popoverOpen = true"
              @keydown.down.prevent="focusNext"
              @keydown.up.prevent="focusPrev"
              @keydown.enter.prevent="selectFocused"
              @keydown.escape="popoverOpen = false"
            />
            <span
              v-if="isLoading"
              class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
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
            </span>
          </div>
        </PopoverAnchor>
        <PopoverContent
          :id="`${field.name}-listbox`"
          role="listbox"
          class="w-[var(--reka-popover-trigger-width)] p-0"
          align="start"
          @keydown.down.prevent="focusNext"
          @keydown.up.prevent="focusPrev"
          @keydown.enter.prevent="selectFocused"
        >
          <div class="border-b p-2">
            <input
              ref="searchInputRef"
              v-model="searchQuery"
              type="text"
              class="border-input bg-background h-9 w-full rounded-md border px-3 py-1 text-sm outline-none"
              placeholder="Digite para filtrar..."
              autocomplete="off"
              @keydown.down.prevent="focusNext"
              @keydown.up.prevent="focusPrev"
              @keydown.enter.prevent="selectFocused"
            />
          </div>
          <ul
            v-if="searchOptions.length > 0"
            class="max-h-60 overflow-auto py-1"
          >
            <li
              v-for="(opt, index) in searchOptions"
              :id="`${field.name}-option-${opt.value}`"
              :key="String(opt.value)"
              role="option"
              :aria-selected="focusedIndex === index"
              :class="[
                'cursor-pointer px-3 py-2 text-sm outline-none',
                focusedIndex === index
                  ? 'bg-accent text-accent-foreground'
                  : 'hover:bg-muted/50',
              ]"
              @click="selectOption(opt)"
              @mouseenter="focusedIndex = index"
            >
              {{ opt.label }}
            </li>
          </ul>
          <p
            v-else-if="!isLoading && searchQuery.length > 0"
            class="px-3 py-4 text-center text-sm text-muted-foreground"
          >
            Nenhum resultado.
          </p>
          <p
            v-else-if="searchQuery.length === 0"
            class="px-3 py-4 text-center text-sm text-muted-foreground"
          >
            Digite acima para buscar.
          </p>
        </PopoverContent>
      </Popover>
    </template>
    <!-- Modo nativo: select estático com field.options (sem busca) -->
    <template v-else>
      <Select
        :model-value="String(modelValue ?? '')"
        @update:model-value="emit('update:modelValue', $event)"
      >
        <SelectTrigger :id="field.name" class="w-full">
          <SelectValue :placeholder="(field.placeholder as string) ?? 'Selecione...'" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem
            v-for="opt in (field.options ?? [])"
            :key="String(opt.value)"
            :value="String(opt.value)"
          >
            {{ opt.label }}
          </SelectItem>
        </SelectContent>
      </Select>
    </template>
  </div>
</template>

<script lang="ts" setup>
import { computed, nextTick, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import {
  Popover,
  PopoverAnchor,
  PopoverContent,
} from '@/components/ui/popover'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import type { FormField } from '@raptor/types'

const props = defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const isSearchMode = computed(() => Boolean(props.field.searchExecuteUrl))

const searchQuery = ref('')
const searchOptions = ref<Array<{ value: string | number; label: string }>>([])
const isLoading = ref(false)
const popoverOpen = ref(false)
const focusedIndex = ref(0)
const inputRef = ref<InstanceType<typeof Input> | null>(null)
const searchInputRef = ref<HTMLInputElement | null>(null)

const activeId = computed(() => {
  const opt = searchOptions.value[focusedIndex.value]
  return opt ? `${props.field.name}-option-${opt.value}` : undefined
})

let debounceTimer: ReturnType<typeof setTimeout> | null = null
const DEBOUNCE_MS = 300

function getCsrfToken(): string | null {
  const name = 'XSRF-TOKEN='
  const ca = document.cookie.split(';')
  for (let i = 0; i < ca.length; i++) {
    const c = ca[i].trim()
    if (c.indexOf(name) === 0) {
      return decodeURIComponent(c.substring(name.length))
    }
  }
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? null
}

function getModelId(): number | string | undefined {
  const page = usePage()
  const p = page.props as Record<string, unknown>
  if (p?.id != null) return p.id as number | string
  const model = p?.product ?? p?.model
  if (model && typeof model === 'object' && model !== null && 'id' in model) {
    return (model as { id: number | string }).id
  }
  return undefined
}

async function fetchOptions(q: string) {
  const url = props.field.searchExecuteUrl
  if (!url) return
  isLoading.value = true
  try {
    const body: { q: string; id?: number | string } = { q }
    const id = getModelId()
    if (id !== undefined) body.id = id
    const token = getCsrfToken()
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    }
    if (token) headers['X-XSRF-TOKEN'] = token
    const res = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers,
      body: JSON.stringify(body),
    })
    if (!res.ok) {
      searchOptions.value = []
      return
    }
    const data = await res.json()
    searchOptions.value = (Array.isArray(data)
      ? data.map((o: { value?: string | number; label?: string } | string | number) => {
          const val =
            typeof o === 'object' && o !== null && 'value' in o
              ? (o as { value?: string | number }).value
              : o
          const label =
            typeof o === 'object' && o !== null && 'label' in o
              ? (o as { label?: string }).label
              : val
          return { value: val as string | number, label: String(label ?? val ?? '') }
        })
      : []) as Array<{ value: string | number; label: string }>
    focusedIndex.value = 0
  } finally {
    isLoading.value = false
  }
}

function onTriggerClick() {
  popoverOpen.value = true
  nextTick(() => {
    searchInputRef.value?.focus()
  })
}

function selectOption(opt: { value: string | number; label: string }) {
  emit('update:modelValue', opt.value)
  searchQuery.value = opt.label
  searchOptions.value = []
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

const selectedLabel = computed(() => {
  if (props.modelValue == null || props.modelValue === '') return ''
  const opts = props.field.options ?? []
  const found = opts.find((o: { value: string | number }) => String(o.value) === String(props.modelValue))
  return found ? String(found.label) : ''
})

watch(
  () => props.modelValue,
  (val) => {
    if (!isSearchMode.value) return
    if (val == null || val === '') {
      searchQuery.value = ''
      return
    }
    const label = selectedLabel.value
    if (label) searchQuery.value = label
  },
  { immediate: true },
)

watch(searchQuery, (q) => {
  if (!isSearchMode.value || !popoverOpen.value) return
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    fetchOptions(q)
    debounceTimer = null
  }, DEBOUNCE_MS)
})
</script>
