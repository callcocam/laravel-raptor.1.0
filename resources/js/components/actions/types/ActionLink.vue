<template>
  <Tooltip v-if="iconOnly && action.label">
    <TooltipTrigger as-child>
      <Button
        :variant="variant"
        size="sm"
        class="h-8 w-8 p-0"
        as-child
      >
        <Link v-if="action.inertia !== false && action.url" :href="action.url">
          <DynamicIcon :name="action.icon" />
        </Link>
        <a v-else-if="action.url" :href="action.url" :target="action.target ?? '_self'">
          <DynamicIcon :name="action.icon" />
        </a>
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
    as-child
  >
    <Link v-if="action.inertia !== false && action.url" :href="action.url">
      <DynamicIcon :name="action.icon" />
      <span class="ml-1">{{ action.label }}</span>
    </Link>
    <a v-else-if="action.url" :href="action.url" :target="action.target ?? '_self'">
      <DynamicIcon :name="action.icon" />
      <span class="ml-1">{{ action.label }}</span>
    </a>
  </Button>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip'
import DynamicIcon from '@raptor/components/ui/DynamicIcon.vue'

export interface ActionLinkProps {
  name: string
  label: string
  icon?: string
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link'
  url?: string
  inertia?: boolean
  target?: string
  [key: string]: unknown
}

const props = defineProps<{
  action: ActionLinkProps
  record?: Record<string, unknown>
  iconOnly?: boolean
}>()

const variant = computed(() => props.action.variant ?? 'ghost')
</script>
