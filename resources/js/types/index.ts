/**
 * Central Type Definitions for Laravel Raptor
 * All TypeScript types and interfaces should be imported from here
 */

// ============================================================================
// Table Types
// ============================================================================

export interface TableColumn {
  name: string
  label: string
  sortable?: boolean
  component?: string
}

export interface TableMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from?: number
  to?: number
}

export interface TableComponents {
  header?: string
  filters?: string
  renderer?: string
  footer?: string
  bulkActions?: string
  actions?: string
  dropdownActions?: string
  pagination?: string
  selectable?: string
  summary?: string
}

export interface TablePayload {
  columns: TableColumn[]
  data: Array<Record<string, unknown>>
  meta: TableMeta
  components?: Record<string, string>
  filters?: Filter[]
  actions?: Record<string, Action>
  headerActions?: Action[]
  bulkActions?: Action[]
  selectable?: boolean
  summary?: Record<string, unknown>
  summaryPosition?: 'top' | 'bottom'
}

// ============================================================================
// Filter Types
// ============================================================================

export interface Filter {
  name: string
  label: string
  component?: string
  options?: Array<{ label: string; value: string | number }>
  [key: string]: any
}

export interface FilterStrategy {
  apply(query: any, column: string, value: any): any
}

// ============================================================================
// Action Types
// ============================================================================

export interface TableAction {
  name: string
  label: string
  component?: string
  actionType?: string
  url?: string
  confirm?: boolean
  to?: string
  target?: string
  inertia?: boolean
  method?: string
  [key: string]: unknown
}

export interface Action {
  name: string
  label: string
  component?: string
  actionType?: string
  url?: string
  confirm?: boolean
  [key: string]: unknown
}

export interface ActionConfirmProps {
  action: Action
  record?: Record<string, unknown>
  column?: Record<string, unknown>
}

export interface ActionProps {
  action: Action
  record?: Record<string, unknown>
  column?: Record<string, unknown>
}

export interface ActionLinkProps {
  action: Action
  record?: Record<string, unknown>
  column?: Record<string, unknown>
}

export interface ActionLinkConfirmProps {
  action: Action
  record?: Record<string, unknown>
  column?: Record<string, unknown>
}

export interface RowActionPayload {
  row: Record<string, unknown>
  action: TableAction
}

// ============================================================================
// DataTable Composable Types
// ============================================================================

export interface UseDataTableOptions {
  /**
   * Tempo de debounce para a busca em ms
   */
  searchDebounce?: number
  /**
   * Se deve preservar o scroll ao navegar
   */
  preserveScroll?: boolean
  /**
   * Se deve preservar o estado ao navegar
   */
  preserveState?: boolean
}

export interface DataTableState {
  search: { value: string }
  currentSort: { value: string | null }
  currentSortDir: { value: 'asc' | 'desc' }
  selectedIds: { value: (string | number)[] }
  isLoading: { value: boolean }
}

export interface DataTableActions {
  onSort: (column: string) => void
  onPage: (page: number) => void
  onPerPage: (perPage: number) => void
  onSearch: (value: string) => void
  onFilterApply: (filterName: string, value: any) => void
  onFiltersReset: () => void
  onHeaderAction: (action: TableAction) => void
  onRowAction: (payload: RowActionPayload) => void
  onBulkAction: (action: TableAction) => void
  clearSelection: () => void
  selectAll: (ids: (string | number)[]) => void
}
