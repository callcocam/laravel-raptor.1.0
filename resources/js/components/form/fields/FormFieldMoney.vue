<template>
  <div :class="inInputGroup ? 'flex flex-1 min-w-0 items-center' : 'flex rounded-md shadow-sm'">
    <div
      :class="inInputGroup
        ? 'inline-flex items-center pl-3 pr-1 text-muted-foreground text-sm font-medium shrink-0'
        : 'inline-flex items-center px-3 rounded-l-md border border-r-0 border-input bg-muted text-muted-foreground text-sm font-medium'"
    >
      {{ currencySymbol }}
    </div>
    <Input
      :id="field.name"
      :data-slot="inInputGroup ? 'input-group-control' : undefined"
      :model-value="displayValue"
      type="text"
      inputmode="decimal"
      :placeholder="formattedPlaceholder"
      :disabled="(field.disabled as boolean) ?? false"
      :readonly="(field.readonly as boolean) ?? false"
      :class="inInputGroup ? 'flex-1 rounded-none border-0 shadow-none focus-visible:ring-0 dark:bg-transparent' : 'w-full rounded-l-none'"
      @input="handleInput"
      @blur="handleBlur"
    />
  </div>
</template>

<script lang="ts" setup>
import { computed, inject, ref, watch } from 'vue'
import type { ComputedRef } from 'vue'
import { Input } from '@/components/ui/input'
import { useFormField } from '@raptor/composables/useFormField'
import type { FormField } from '@raptor/types'


const props = defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const inInputGroup = inject<ComputedRef<boolean>>('inInputGroup', { value: false } as ComputedRef<boolean>)

const { emitChange } = useFormField(emit)

const displayValue = ref('')

const currency = computed(() => (props.field.currency as string) || 'BRL')
const decimals = computed(() => (props.field.decimals as number) ?? 2)
const decimalSeparator = computed(() => (props.field.decimalSeparator as string) || ',')
const thousandsSeparator = computed(() => (props.field.thousandsSeparator as string) || '.')

const currencySymbols: Record<string, string> = {
  BRL: 'R$',
  USD: '$',
  EUR: '€',
  GBP: '£',
  JPY: '¥',
  ARS: '$',
  CLP: '$',
  MXN: '$',
  PEN: 'S/',
  UYU: '$U',
}

const currencySymbol = computed(() => currencySymbols[currency.value] ?? currency.value)

const formattedPlaceholder = computed(() => {
  const p = props.field.placeholder
  if (p != null && String(p).trim() !== '') return String(p)
  return formatMoney(0)
})

function formatMoney(value: number | null): string {
  if (value === null || Number.isNaN(value)) return ''
  const parts = value.toFixed(decimals.value).split('.')
  const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator.value)
  const decimalPart = parts[1] ?? '0'.repeat(decimals.value)
  return `${integerPart}${decimalSeparator.value}${decimalPart}`
}

function parseMoneyToFloat(value: string): number | null {
  if (!value || value.trim() === '') return null
  let normalized = value.replace(new RegExp(`\\${thousandsSeparator.value}`, 'g'), '')
  normalized = normalized.replace(new RegExp(`\\${decimalSeparator.value}`, 'g'), '.')
  normalized = normalized.replace(/[^\d.\-]/g, '')
  const parsed = parseFloat(normalized)
  return Number.isNaN(parsed) ? null : parsed
}

watch(
  () => props.modelValue,
  (newValue) => {
    if (newValue === null || newValue === undefined || newValue === '') {
      displayValue.value = ''
    } else {
      const numValue =
        typeof newValue === 'string' ? parseMoneyToFloat(newValue) : Number(newValue)
      displayValue.value = numValue !== null ? formatMoney(numValue) : ''
    }
  },
  { immediate: true },
)

function handleInput(event: Event): void {
  const target = event.target as HTMLInputElement
  const inputValue = target.value
  const numbersOnly = inputValue.replace(/\D/g, '')
  if (!numbersOnly) {
    displayValue.value = ''
    emitChange(null)
    return
  }
  const numValue = Number.parseInt(numbersOnly, 10) / Math.pow(10, decimals.value)
  const formatted = formatMoney(numValue)
  displayValue.value = formatted
  emitChange(formatted)
  setTimeout(() => {
    target.setSelectionRange(formatted.length, formatted.length)
  }, 0)
}

function handleBlur(): void {
  if (!displayValue.value) {
    emitChange(null)
    return
  }
  emitChange(displayValue.value)
}
</script>
