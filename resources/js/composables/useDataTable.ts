import { ref, computed, watch, type Ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import type {
  UseDataTableOptions,
  TableMeta,
  TableAction,
  RowActionPayload,
  DataTableState,
  DataTableActions,
} from '@raptor/types'

/**
 * Composable para gerenciar estado e ações do DataTable
 * 
 * @example
 * ```ts
 * const { state, actions } = useDataTable()
 * 
 * // No template:
 * <DataTable
 *   v-bind="state"
 *   @sort="actions.onSort"
 *   @page="actions.onPage"
 *   ...
 * />
 * ```
 */
export function useDataTable(options: UseDataTableOptions = {}) {
  const {
    searchDebounce = 300,
    preserveScroll = true,
    preserveState = true,
  } = options

  const page = usePage()

  // Extrai query params da URL atual
  function getQueryParams(): Record<string, string> {
    if (typeof window === 'undefined') return {}
    const params = new URLSearchParams(window.location.search)
    const result: Record<string, string> = {}
    params.forEach((value, key) => {
      result[key] = value
    })
    return result
  }

  // Estado inicial baseado na URL
  const query = getQueryParams()
  
  const search = ref(query.search ?? '')
  const currentSort = ref<string | null>(query.sort ?? null)
  const currentSortDir = ref<'asc' | 'desc'>((query.sort_dir as 'asc' | 'desc') ?? 'asc')
  const selectedIds = ref<(string | number)[]>([])
  const isLoading = ref(false)

  let searchDebounceTimer: ReturnType<typeof setTimeout> | null = null

  /**
   * Navega para a URL com os parâmetros atualizados
   */
  function navigate(updates: Record<string, string | number | null | undefined>) {
    const currentQuery = getQueryParams()
    const newQuery = { ...currentQuery, ...updates }

    // Remove valores nulos/undefined/vazios
    Object.keys(newQuery).forEach(key => {
      if (newQuery[key] === null || newQuery[key] === undefined || newQuery[key] === '') {
        delete newQuery[key]
      }
    })

    const queryString = new URLSearchParams(newQuery as Record<string, string>).toString()
    const url = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname

    isLoading.value = true
    
    router.visit(url, {
      preserveScroll,
      preserveState,
      onFinish: () => {
        isLoading.value = false
      },
    })
  }

  /**
   * Ordena por coluna
   */
  function onSort(column: string) {
    const newDir = currentSort.value === column && currentSortDir.value === 'asc' ? 'desc' : 'asc'
    currentSort.value = column
    currentSortDir.value = newDir
    navigate({ sort: column, sort_dir: newDir, page: 1 })
  }

  /**
   * Navega para página específica
   */
  function onPage(pageNum: number) {
    navigate({ page: pageNum })
  }

  /**
   * Altera itens por página
   */
  function onPerPage(perPage: number) {
    navigate({ per_page: perPage, page: 1 })
  }

  /**
   * Busca com debounce
   */
  function onSearch(value: string) {
    search.value = value

    if (searchDebounceTimer) {
      clearTimeout(searchDebounceTimer)
    }

    searchDebounceTimer = setTimeout(() => {
      navigate({ search: value || null, page: 1 })
    }, searchDebounce)
  }

  /**
   * Aplica um filtro individual
   */
  function onFilterApply(filterName: string, value: any) {
    navigate({ [filterName]: value, page: 1 })
  }

  /**
   * Reseta todos os filtros
   */
  function onFiltersReset() {
    search.value = ''
    currentSort.value = null
    currentSortDir.value = 'asc'
    // Navigate to clean URL, removing all query params
    router.visit(window.location.pathname, {
      preserveScroll,
      preserveState: false,
      onFinish: () => { isLoading.value = false },
    })
  }

  /**
   * Executa ação do header (criar, exportar, etc.)
   */
  function onHeaderAction(action: TableAction) {
    if (action.url) {
      if (action.inertia !== false) {
        router.visit(action.url, {
          method: (action.method as 'get' | 'post') ?? 'get',
        })
      } else {
        window.location.href = action.url
      }
    }
  }

  /**
   * Executa ação de linha (view, edit, delete, etc.)
   */
  function onRowAction(payload: RowActionPayload) {
    const { action } = payload

    // Para ações com URL e navegação, o componente de ação já cuida
    // Aqui podemos adicionar lógica customizada se necessário
    if (action.url && action.inertia !== false) {
      // A navegação já é feita pelo componente ActionLink/ActionConfirm
      return
    }
  }

  /**
   * Executa ação em massa nos itens selecionados
   */
  function onBulkAction(action: TableAction) {
    if (!selectedIds.value.length) return
console.log('Executando ação em massa:', action.name, action, 'IDs:', selectedIds.value)
    if (action.url) {
      router.visit(action.url, {
        method: (action.method as 'get' | 'post' | 'delete') ?? 'post',
        data: {
          ids: selectedIds.value,
        },
        onSuccess: () => {
          selectedIds.value = []
        },
      })
    }
  }

  /**
   * Limpa seleção
   */
  function clearSelection() {
    selectedIds.value = []
  }

  /**
   * Seleciona todos os IDs fornecidos
   */
  function selectAll(ids: (string | number)[]) {
    selectedIds.value = [...ids]
  }

  // Watch para sincronizar search com a URL quando muda externamente
  watch(search, (val) => {
    // Já tratado pelo onSearch com debounce
  })

  const state: DataTableState = {
    search,
    currentSort,
    currentSortDir,
    selectedIds,
    isLoading,
  }

  const actions: DataTableActions = {
    onSort,
    onPage,
    onPerPage,
    onSearch,
    onFilterApply,
    onFiltersReset,
    onHeaderAction,
    onRowAction,
    onBulkAction,
    clearSelection,
    selectAll,
  }

  return {
    state,
    actions,
    // Atalhos diretos para facilitar v-model
    search,
    currentSort,
    currentSortDir,
    selectedIds,
    isLoading,
    // Atalhos para ações
    ...actions,
  }
}

export default useDataTable
