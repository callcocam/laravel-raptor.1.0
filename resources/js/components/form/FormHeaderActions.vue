<template>
  <div class="flex items-center gap-2">
    <Button
      v-for="action in actions"
      :key="action.name"
      :variant="(action.variant as 'default' | 'outline' | 'ghost' | 'destructive' | 'link') ?? 'default'"
      :disabled="action.disabled"
      @click="handleAction(action)"
    >
      {{ action.label }}
    </Button>
  </div>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import type { FormActionPayload } from '@raptor/types'

const props = defineProps<{
  actions: FormActionPayload[]
}>()

function handleAction(action: FormActionPayload) {
  if (action.type === 'url' && action.url) {
    if (action.inertia) {
      router.visit(action.url)
    } else {
      window.location.href = action.url
    }
    return
  }
  if (action.type === 'confirm') {
    // TODO: abrir modal de confirmação e executar executeUsing
    return
  }
}
</script>
