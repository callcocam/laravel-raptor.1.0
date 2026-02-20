<template>
  <AlertDialog>
    <Tooltip v-if="iconOnly && action.label">
      <TooltipTrigger as-child>
        <AlertDialogTrigger as-child>
          <Button :variant="variant" size="sm" class="h-8 w-8 p-0">
            <DynamicIcon :name="action.icon" />
          </Button>
        </AlertDialogTrigger>
      </TooltipTrigger>
      <TooltipContent>
        <p>{{ action.label }}</p>
      </TooltipContent>
    </Tooltip>
    <AlertDialogTrigger v-else as-child>
      <Button :variant="variant" size="sm" class="h-8">
        <DynamicIcon :name="action.icon" />
        <span class="ml-1">{{ action.label }}</span>
      </Button>
    </AlertDialogTrigger>
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>{{ action.title ?? 'Confirmar ação' }}</AlertDialogTitle>
        <AlertDialogDescription>
          {{ action.description ?? 'Tem certeza que deseja executar esta ação?' }}
        </AlertDialogDescription>
      </AlertDialogHeader>
      <div v-if="action.requireTextConfirmation" class="py-4">
        <p class="text-sm text-muted-foreground mb-2">
          Digite <span class="font-semibold text-foreground">{{ action.confirmationText }}</span> para confirmar:
        </p>
        <Input
          v-model="confirmationInput"
          :placeholder="action.confirmationPlaceholder ?? 'Digite para confirmar'"
          class="w-full"
        />
      </div>
      <AlertDialogFooter>
        <AlertDialogCancel @click="confirmationInput = ''">{{ action.cancelText ?? 'Cancelar' }}</AlertDialogCancel>
        <AlertDialogAction
          :class="confirmButtonClass"
          :disabled="!canConfirm"
          @click="handleConfirm"
        >
          {{ action.confirmText ?? 'Confirmar' }}
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import DynamicIcon from '@raptor/components/ui/DynamicIcon.vue'

export interface ActionConfirmProps {
  name: string
  label: string
  icon?: string
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link'
  url?: string
  executeUrl?: string
  method?: 'get' | 'post' | 'put' | 'patch' | 'delete'
  title?: string
  description?: string
  confirmText?: string
  cancelText?: string
  confirmVariant?: 'default' | 'destructive'
  requireTextConfirmation?: boolean
  confirmationText?: string
  confirmationPlaceholder?: string
  [key: string]: unknown
}

const props = defineProps<{
  action: ActionConfirmProps
  record?: Record<string, unknown>
  iconOnly?: boolean
}>()

const emit = defineEmits<{
  (e: 'click', event: Event): void
}>()

const confirmationInput = ref('')

const variant = computed(() => props.action.variant ?? 'ghost')

const canConfirm = computed(() => {
  if (!props.action.requireTextConfirmation) return true
  return confirmationInput.value === props.action.confirmationText
})

const confirmButtonClass = computed(() => {
  if (props.action.confirmVariant === 'destructive') {
    return 'bg-destructive text-destructive-foreground hover:bg-destructive/90'
  }
  return ''
})

function handleConfirm() {
  const url = props.action.executeUrl ?? props.action.url
  if (url) {
    const method = props.action.method ?? 'delete'
    confirmationInput.value = ''
    console.log(`Confirmando ação "${props.action.name}" com URL:`, url, 'e método:', method)
    // Se houver selectedIds no record (para bulk actions), envia como POST data
    if (props.record && Array.isArray(props.record.selectedIds) && props.record.selectedIds.length > 0) {
      router.post(url, {
        ids: props.record.selectedIds,
      }, {
        preserveScroll: true,
        preserveState: false
      })
    } else {
      router.visit(url, {
        method,
        preserveScroll: true,
      })
    }
  }
}
</script>
