<template>
  <div class="flex items-center justify-between gap-2">
    <div class="flex flex-col gap-1">
      <h2 v-if="title" class="text-2xl font-semibold tracking-tight">{{ title }}</h2>
      <p v-if="subtitle" class="text-muted-foreground text-sm">{{ subtitle }}</p>
    </div>
    <div v-if="headerActions && headerActions.length" class="flex items-center gap-2">
      <Button
        v-for="action in headerActions"
        :key="action.name"
        @click="handleAction(action)"
      >
        {{ action.label }}
      </Button>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { Button } from '@/components/ui/button'

const props = defineProps<{
  title?: string
  subtitle?: string
  headerActions?: Array<{ name: string; label: string; url?: string | null; inertia?: boolean }>
}>()

const emit = defineEmits<{ action: [payload: { name: string; label: string; url?: string | null }] }>()

function handleAction(action: { name: string; label: string; url?: string | null; inertia?: boolean }) {
  if (action.inertia && action.url) {
    emit('action', { name: action.name, label: action.label, url: action.url })
    return
  }
  if (action.url && !action.inertia) {
    window.location.href = action.url
  }
}
</script>
