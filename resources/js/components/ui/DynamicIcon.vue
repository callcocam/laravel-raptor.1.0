<template>
  <component :is="iconComponent" v-if="iconComponent" :class="computedClass" />
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import * as LucideIcons from 'lucide-vue-next'

const props = withDefaults(
  defineProps<{
    name?: string | null
    class?: string
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
  }>(),
  {
    name: null,
    class: '',
    size: 'sm',
  }
)

const sizeClasses: Record<string, string> = {
  xs: 'h-3 w-3',
  sm: 'h-4 w-4',
  md: 'h-5 w-5',
  lg: 'h-6 w-6',
  xl: 'h-8 w-8',
}

const iconComponent = computed(() => {
  if (!props.name) return null
  
  // Tenta o nome exato primeiro (ex: "Eye", "Trash2")
  const exactName = props.name as keyof typeof LucideIcons
  if (LucideIcons[exactName]) {
    return LucideIcons[exactName]
  }
  
  // Tenta converter kebab-case para PascalCase (ex: "arrow-left" -> "ArrowLeft")
  const pascalName = props.name
    .split('-')
    .map(part => part.charAt(0).toUpperCase() + part.slice(1).toLowerCase())
    .join('') as keyof typeof LucideIcons
  
  if (LucideIcons[pascalName]) {
    return LucideIcons[pascalName]
  }
  
  // Tenta adicionar sufixo "Icon" se necessário
  const withIconSuffix = `${pascalName}Icon` as keyof typeof LucideIcons
  if (LucideIcons[withIconSuffix]) {
    return LucideIcons[withIconSuffix]
  }
  
  return null
})

const computedClass = computed(() => {
  const sizeClass = sizeClasses[props.size] ?? sizeClasses.sm
  return props.class ? `${sizeClass} ${props.class}` : sizeClass
})
</script>
