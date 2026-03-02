<template>
  <div class="space-y-2">
    <Label
      v-if="!labelRenderedByWrapper && field.label"
      :for="field.name"
    >
      {{ field.label }}
    </Label>
    <div class="relative flex gap-1">
      <Input
        :id="field.name"
        :model-value="modelValue ?? ''"
        :type="visible ? 'text' : 'password'"
        :placeholder="(field.placeholder as string) ?? undefined"
        autocomplete="off"
        class="w-full pr-20"
        @update:model-value="emitChange($event)"
      />
      <div class="absolute right-1 top-1/2 flex -translate-y-1/2 items-center gap-0.5">
        <button
          type="button"
          class="rounded p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground"
          :aria-label="visible ? 'Ocultar senha' : 'Mostrar senha'"
          @click="visible = !visible"
        >
          <Eye v-if="visible" class="h-4 w-4" />
          <EyeOff v-else class="h-4 w-4" />
        </button>
        <button
          type="button"
          class="rounded p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground"
          aria-label="Sugerir senha"
          title="Gerar senha forte"
          @click="suggestPassword"
        >
          <KeyRound class="h-4 w-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { inject, ref } from 'vue'
import { Eye, EyeOff, KeyRound } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useFormField } from '@raptor/composables/useFormField'
import type { FormField } from '@raptor/types'

const labelRenderedByWrapper = inject('fieldLabelRenderedByWrapper', false)

const props = defineProps<{
  field: FormField
  modelValue: unknown
}>()

const emit = defineEmits<{
  'update:modelValue': [value: unknown]
}>()

const { emitChange } = useFormField(emit)

const visible = ref(false)

const LOWER = 'abcdefghijklmnopqrstuvwxyz'
const UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
const DIGITS = '0123456789'
const SYMBOLS = '!@#$%&*'

function generatePassword(length = 14): string {
  const pool = LOWER + UPPER + DIGITS + SYMBOLS
  let result = ''
  const getRandom = (s: string) => s[Math.floor(Math.random() * s.length)]
  result += getRandom(LOWER)
  result += getRandom(UPPER)
  result += getRandom(DIGITS)
  result += getRandom(SYMBOLS)
  for (let i = 4; i < length; i++) {
    result += pool[Math.floor(Math.random() * pool.length)]
  }
  return result
    .split('')
    .sort(() => Math.random() - 0.5)
    .join('')
}

function suggestPassword(): void {
  const password = generatePassword()
  emitChange(password)
  visible.value = true
}
</script>
