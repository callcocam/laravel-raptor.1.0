<template>
  <Tooltip v-if="iconOnly && action.label">
    <TooltipTrigger as-child>
      <Button
        :variant="variant"
        size="sm"
        class="h-6 w-6 p-0"
        @click="handleClick"
      >
        <DynamicIcon :name="action.icon" />
      </Button>
    </TooltipTrigger>
    <TooltipContent>
      <p>{{ action.label }}</p>
    </TooltipContent>
  </Tooltip>
  <Button
    v-else
    :variant="variant"
    size="sm"
    class="h-8"
    @click="handleClick"
  >
    <DynamicIcon :name="action.icon" />
    <span class="ml-1">{{ action.label }}</span>
  </Button>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip'
import DynamicIcon from '@raptor/components/ui/DynamicIcon.vue'

export interface ActionProps {
  name: string
  label: string
  icon?: string
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link'
  url?: string
  [key: string]: unknown
}

const props = defineProps<{
  action: ActionProps
  record?: Record<string, unknown>
  iconOnly?: boolean
}>()

const emit = defineEmits<{
  (e: 'click', event: Event): void
}>()

const variant = computed(() => props.action.variant ?? 'ghost')

function handleClick(event: Event) {
  emit('click', event)
}
</script>
