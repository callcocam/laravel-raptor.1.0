<template>
  <DropdownMenu v-if="actionsList.length">
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="h-8 w-8 p-0">
        <MoreHorizontal class="h-4 w-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem
        v-for="act in actionsList"
        :key="act.name"
        as-child
      >
        <ActionRenderer :action="act" :record="record" @click="emit('action', act)" />
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { MoreHorizontal } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import ActionRenderer from '@raptor/components/actions/ActionRenderer.vue'

const props = defineProps<{
  actions: Record<string, { name: string; label: string; url?: string }> | Array<{ name: string; label: string; url?: string }>
  record: Record<string, unknown>
}>()

const emit = defineEmits<{
  (e: 'action', action: { name: string; label: string; url?: string }): void
}>()

const actionsList = computed(() =>
  Array.isArray(props.actions) ? props.actions : Object.values(props.actions ?? {})
)
</script>
