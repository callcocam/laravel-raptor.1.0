<template>
    <div class="relative">
        <DropdownMenu v-if="actions.length">
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" class="h-8 w-8 p-0">
                    <MoreHorizontal class="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuItem
                    v-for="act in actions"
                    :key="act.name" 
                    
                >
                   <ActionRenderer :action="act" @row-action="emit('rowAction', act)" />
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
<script lang="ts" setup>
import { MoreHorizontal } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

defineProps<{
    actions: Array<{ name: string; label: string; url?: string | null }>;
}>();

const emit = defineEmits<{
    (
        e: 'rowAction',
        value: { name: string; label: string; url?: string | null },
    ): void;
}>();
</script>
