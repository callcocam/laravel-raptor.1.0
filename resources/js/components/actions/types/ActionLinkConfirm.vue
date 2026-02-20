<template>
    <AlertDialog>
        <Tooltip v-if="iconOnly && action.label">
            <TooltipTrigger as-child>
                <AlertDialogTrigger as-child>
                    <Button :variant="variant" size="sm" class="h-6 w-6 p-0">
                        <DynamicIcon :name="action.icon" />
                    </Button>
                </AlertDialogTrigger>
            </TooltipTrigger>
            <TooltipContent>
                <p>{{ action.label }}</p>
            </TooltipContent>
        </Tooltip>
        <AlertDialogTrigger v-else as-child>
            <Button :variant="variant" size="sm">
                <DynamicIcon :name="action.icon" />
                <span class="ml-1">{{ action.label }}</span>
            </Button>
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{
                    action.title ?? 'Confirmar ação'
                }}</AlertDialogTitle>
                <AlertDialogDescription>
                    {{
                        action.description ??
                        'Tem certeza que deseja executar esta ação?'
                    }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <div v-if="action.requireTextConfirmation" class="py-4">
                <p class="mb-2 text-sm text-muted-foreground">
                    Digite
                    <span class="font-semibold text-foreground">{{
                        action.confirmationText
                    }}</span>
                    para confirmar:
                </p>
                <Input
                    v-model="confirmationInput"
                    :placeholder="
                        action.confirmationPlaceholder ??
                        'Digite para confirmar'
                    "
                    class="w-full"
                />
            </div>
            <AlertDialogFooter>
                <AlertDialogCancel @click="confirmationInput = ''">{{
                    action.cancelText ?? 'Cancelar'
                }}</AlertDialogCancel>
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
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';
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
} from '@/components/ui/alert-dialog';
import DynamicIcon from '@raptor/components/ui/DynamicIcon.vue';

export interface ActionLinkConfirmProps {
    name: string;
    label: string;
    icon?: string;
    variant?:
        | 'default'
        | 'destructive'
        | 'outline'
        | 'secondary'
        | 'ghost'
        | 'link';
    to?: string;
    url?: string;
    inertia?: boolean;
    title?: string;
    description?: string;
    confirmText?: string;
    cancelText?: string;
    confirmVariant?: 'default' | 'destructive';
    requireTextConfirmation?: boolean;
    confirmationText?: string;
    confirmationPlaceholder?: string;
    [key: string]: unknown;
}

const props = defineProps<{
    action: ActionLinkConfirmProps;
    record?: Record<string, unknown>;
    iconOnly?: boolean;
}>();

const confirmationInput = ref('');

const variant = computed(() => props.action.variant ?? 'ghost');

const canConfirm = computed(() => {
    if (!props.action.requireTextConfirmation) return true;
    return confirmationInput.value === props.action.confirmationText;
});

const confirmButtonClass = computed(() => {
    if (props.action.confirmVariant === 'destructive') {
        return 'bg-destructive text-destructive-foreground hover:bg-destructive/90';
    }
    return '';
});

// Função para adicionar query params atuais à URL
function getUrlWithQueryParams(baseUrl?: string): string {
  if (!baseUrl) return ''
  
  const url = new URL(baseUrl, window.location.origin)
  const searchParams = new URLSearchParams(window.location.search)
  searchParams.forEach((value, key) => {
    if (!url.searchParams.has(key)) {
      url.searchParams.append(key, value)
    }
  })
  
  return url.toString()
}

function handleConfirm() {
    const url = props.action.to ?? props.action.url;
    if (url) {
        confirmationInput.value = '';
        const urlWithParams = getUrlWithQueryParams(url)
        if (props.action.inertia !== false) {
            router.visit(urlWithParams);
        } else {
            window.location.href = urlWithParams;
        }
    }
}
</script>
