<template>
  <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
    <Card
      v-for="metric in metrics"
      :key="metric.key"
      class="rounded-lg border shadow-sm"
    >
      <CardContent class="flex items-start gap-3 p-4">
        <div
          class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-muted"
          aria-hidden
        >
          <DynamicIcon :name="metric.icon" class="h-4 w-4 text-muted-foreground" size="sm" />
        </div>
        <div class="min-w-0 flex-1 space-y-1">
          <p class="text-xs font-medium text-muted-foreground">
            {{ metric.label }}
          </p>
          <div class="flex flex-wrap items-baseline gap-x-3 gap-y-0.5 text-sm">
            <span class="font-semibold">
              {{ formatSummaryWithAffixes(metric.prefix, metric.pageValue, metric.suffix) }}
              <span class="ml-1 font-normal text-muted-foreground">pág.</span>
            </span>
            <span class="text-muted-foreground">/</span>
            <span class="font-semibold">
              {{ formatSummaryWithAffixes(metric.prefix, metric.globalValue, metric.suffix) }}
              <span class="ml-1 font-normal text-muted-foreground">total</span>
            </span>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import DynamicIcon from '@raptor/components/ui/DynamicIcon.vue'

type SummaryItem = {
  value: unknown
  label?: string
  column?: string
  function?: string
  prefix?: string | null
  suffix?: string | null
}

type MetricRow = {
  key: string
  label: string
  icon: string
  prefix: string | null
  suffix: string | null
  pageValue: unknown
  globalValue: unknown
}

const functionIcons: Record<string, string> = {
  count: 'Hash',
  sum: 'CircleDollarSign',
  avg: 'TrendingUp',
  min: 'ArrowDown',
  max: 'ArrowUp',
}

const props = defineProps<{
  summary?: Record<string, Record<string, SummaryItem>>
}>()

const metrics = computed<MetricRow[]>(() => {
  if (!props.summary || typeof props.summary !== 'object') {
    return []
  }

  const page = (props.summary.page ?? {}) as Record<string, SummaryItem>
  const global = (props.summary.global ?? {}) as Record<string, SummaryItem>
  const allKeys = new Set([...Object.keys(page), ...Object.keys(global)])

  return Array.from(allKeys)
    .map((key) => {
      const pageItem = page[key]
      const globalItem = global[key]
      const label = pageItem?.label ?? globalItem?.label ?? key
      const fn = pageItem?.function ?? globalItem?.function ?? ''
      const prefix = pageItem?.prefix ?? globalItem?.prefix ?? null
      const suffix = pageItem?.suffix ?? globalItem?.suffix ?? null

      return {
        key,
        label,
        icon: functionIcons[fn] ?? 'BarChart2',
        prefix: prefix ?? null,
        suffix: suffix ?? null,
        pageValue: pageItem?.value,
        globalValue: globalItem?.value,
      }
    })
    .filter((m) => m.pageValue != null || m.globalValue != null)
})

function formatSummaryValue(value: unknown): string {
  if (value === null || value === undefined || value === '') {
    return '-'
  }

  if (typeof value === 'number') {
    return new Intl.NumberFormat('pt-BR', {
      maximumFractionDigits: 2,
      minimumFractionDigits: 0,
    }).format(value)
  }

  return String(value)
}

function formatSummaryWithAffixes(
  prefix: string | null,
  value: unknown,
  suffix: string | null
): string {
  const formatted = formatSummaryValue(value)
  const pre = prefix ? `${prefix} ` : ''
  const suf = suffix ? ` ${suffix}` : ''
  return `${pre}${formatted}${suf}`.trim() || '-'
}
</script>
