import type { App, Plugin } from 'vue'
import { defineAsyncComponent } from 'vue'
import type { Component } from 'vue'
// import ActionRegistry from '../utils/ActionRegistry'
// import FilterRegistry from '../utils/FilterRegistry'
// import TableRegistry from '../utils/TableRegistry' 
import ComponentRegistry from '@raptor/utils/ComponentRegistry'

/**
 * Auto-registro de componentes padrão do InfoList
 *
 * Estes componentes são registrados automaticamente e podem ser
 * sobrescritos pela aplicação se necessário.
 */
ComponentRegistry.registerBulk({
    // 'info-column-text': defineAsyncComponent(() => import('~/components/infolist/columns/InfolistText.vue')),
    // 'info-column-email': defineAsyncComponent(() => import('~/components/infolist/columns/InfolistEmail.vue')),
    // 'info-column-date': defineAsyncComponent(() => import('~/components/infolist/columns/InfolistDate.vue')),
    // 'info-column-phone': defineAsyncComponent(() => import('~/components/infolist/columns/InfolistPhone.vue')),
    // 'info-column-status': defineAsyncComponent(() => import('~/components/infolist/columns/InfolistStatus.vue')),
    // 'info-column-boolean': defineAsyncComponent(() => import('~/components/infolist/columns/InfolistBoolean.vue')),
    // 'info-column-card': defineAsyncComponent(() => import('~/components/infolist/columns/InfolistCard.vue')),
    // 'info-column-link': defineAsyncComponent(() => import('~/components/infolist/columns/InfolistLink.vue')),
    // 'info-column-has-many': defineAsyncComponent(() => import('~/components/infolist/columns/InfolistHasMany.vue')),
    // 'info-column-belongs-to-many': defineAsyncComponent(() => import('~/components/infolist/columns/InfolistBelongsToMany.vue')),
})

/**
 * Auto-registro de componentes de estrutura da Table
 *
 * Estes componentes são registrados automaticamente e podem ser
 * sobrescritos pela aplicação se necessário.
 */
ComponentRegistry.registerBulk({
    'table-header': defineAsyncComponent(() => import('@raptor/components/table/TableHeader.vue')),
    'table-filters': defineAsyncComponent(() => import('@raptor/components/table/TableFilters.vue')),
    'table-renderer': defineAsyncComponent(() => import('@raptor/components/table/TableRenderer.vue')),
    'table-footer': defineAsyncComponent(() => import('@raptor/components/table/TableFooter.vue')),
    'table-bulk-action-inline': defineAsyncComponent(() => import('@raptor/components/table/actions/TableBulkActionInline.vue')),
    'data-table': defineAsyncComponent(() => import('@raptor/components/table/DataTable.vue')),
})

/**
 * Auto-registro de componentes padrão de Table
 *
 * Estes componentes são registrados automaticamente e podem ser
 * sobrescritos pela aplicação se necessário.
 */
ComponentRegistry.registerBulk({
    'text-table-column': defineAsyncComponent(() => import('@raptor/components/table/columns/TextTableColumn.vue')),
    'image-table-column': defineAsyncComponent(() => import('@raptor/components/table/columns/ImageTableColumn.vue')),
    'datetime-table-column': defineAsyncComponent(() => import('@raptor/components/table/columns/DateTimeTableColumn.vue')),
    'link-table-column': defineAsyncComponent(() => import('@raptor/components/table/columns/LinkTableColumn.vue')),
    'badge-table-column': defineAsyncComponent(() => import('@raptor/components/table/columns/BadgeTableColumn.vue')),  
    'editable-text-input': defineAsyncComponent(() => import('@raptor/components/table/columns/editable/EditableTextInput.vue')),
    'editable-textarea': defineAsyncComponent(() => import('@raptor/components/table/columns/editable/EditableTextarea.vue')),
    'editable-select': defineAsyncComponent(() => import('@raptor/components/table/columns/editable/EditableSelect.vue')), 
    'table-action-inline': defineAsyncComponent(() => import('@raptor/components/table/actions/TableActionInline.vue')),
    'table-action-dropdown': defineAsyncComponent(() => import('@raptor/components/table/actions/TableActionDropdown.vue')),
    // 'table-column-text': defineAsyncComponent(() => import('@raptor/components/table/columns/TableText.vue')),
    // 'table-column-text-editable': defineAsyncComponent(() => import('@raptor/components/table/columns/editable/TableText.vue')),
    // 'table-column-email': defineAsyncComponent(() => import('@raptor/components/table/columns/TableEmail.vue')),
    // 'table-column-email-editable': defineAsyncComponent(() => import('@raptor/components/table/columns/editable/TableEmail.vue')),
    // 'table-column-date': defineAsyncComponent(() => import('@raptor/components/table/columns/TableDate.vue')),
    // 'table-column-date-editable': defineAsyncComponent(() => import('@raptor/components/table/columns/editable/TableDate.vue')),
    // 'table-column-phone': defineAsyncComponent(() => import('@raptor/components/table/columns/TablePhone.vue')),
    // 'table-column-phone-editable': defineAsyncComponent(() => import('@raptor/components/table/columns/editable/TablePhone.vue')),
    // 'table-column-status': defineAsyncComponent(() => import('@raptor/components/table/columns/TableStatus.vue')),
    // 'table-column-status-editable': defineAsyncComponent(() => import('@raptor/components/table/columns/editable/TableStatus.vue')),
    // 'table-column-boolean': defineAsyncComponent(() => import('@raptor/components/table/columns/TableBoolean.vue')),
    // 'table-column-boolean-editable': defineAsyncComponent(() => import('@raptor/components/table/columns/editable/TableBoolean.vue')),
    // 'table-column-image': defineAsyncComponent(() => import('@raptor/components/table/columns/TableImage.vue')),
    // 'table-column-image-editable': defineAsyncComponent(() => import('@raptor/components/table/columns/editable/TableImage.vue')),
})

/**
 * Auto-registro de componentes de formulário
 *
 * Estes componentes são usados em formulários e modais de ações
 */

// New Field-based components (recommended)
ComponentRegistry.registerBulk({
    // 'form-field-text': defineAsyncComponent(() => import('@raptor/components/form/fields/FormFieldText.vue')), 
})

/**
 * Auto-registro de componentes padrão do Breadcrumb
 *
 * Estes componentes são registrados automaticamente e podem ser
 * sobrescritos pela aplicação se necessário.
 */
// BreadcrumbRegistry.registerBulk({
//     'breadcrumb-default': defineAsyncComponent(() => import('@raptor/components/breadcrumbs/DefaultBreadcrumb.vue')),
//     'breadcrumb-page-header': defineAsyncComponent(() => import('@raptor/components/breadcrumbs/PageHeaderBreadcrumb.vue')),
// })

// BreadcrumbRegistry.markAsInitialized()

/**
 * Auto-registro de componentes padrão da Table
 *
 * Estes componentes são registrados automaticamente e podem ser
 * sobrescritos pela aplicação se necessário.
 */ 

// TableRegistry.registerBulk({
//     'table-default': defineAsyncComponent(() => import('@raptor/components/table/DefaultTable.vue')),
// })

// TableRegistry.markAsInitialized()

/**
 * Auto-registro de componentes padrão de Actions
 *
 * Estes componentes são registrados automaticamente e podem ser
 * sobrescritos pela aplicação se necessário.
 */ 

ComponentRegistry.registerBulk({
    'action-button': defineAsyncComponent(() => import('@raptor/components/actions/types/ActionButton.vue')),
    'action-link': defineAsyncComponent(() => import('@raptor/components/actions/types/ActionLink.vue')),
    'action-confirm': defineAsyncComponent(() => import('@raptor/components/actions/types/ActionConfirm.vue')),
    'action-link-confirm': defineAsyncComponent(() => import('@raptor/components/actions/types/ActionLinkConfirm.vue')),
})

/**
 * Auto-registro de componentes padrão de Filters
 *
 * Estes componentes são registrados automaticamente e podem ser
 * sobrescritos pela aplicação se necessário.
 */ 

ComponentRegistry.registerBulk({
    'filter-select': defineAsyncComponent(() => import('@raptor/components/table/filters/FilterSelect.vue')),
    'filter-trashed': defineAsyncComponent(() => import('@raptor/components/table/filters/FilterTrashed.vue')),
})

/**
 * Opções do RaptorPlugin
 */
export interface RaptorPluginOptions {
    customFormatters?: Record<string, Function>
    customComponents?: Record<string, Component>
    overrideComponents?: {
        actions?: Record<string, Component>
        filters?: Record<string, Component>
        tables?: Record<string, Component>
        infolist?: Record<string, Component>
        forms?: Record<string, Component>
        breadcrumbs?: Record<string, Component>
    }
}

/**
 * RaptorPlugin - Plugin Vue para Laravel Raptor
 *
 * Registra todos os componentes padrão e permite customização
 * via overrideComponents e customComponents
 */
const install = (app: App, options: RaptorPluginOptions = {}): void => {
    if (options.overrideComponents) {
        if (options.overrideComponents.infolist) {
            Object.entries(options.overrideComponents.infolist).forEach(([name, component]) => {
                ComponentRegistry.register(name, component)
            })
        }
        if (options.overrideComponents.forms) {
            Object.entries(options.overrideComponents.forms).forEach(([name, component]) => {
                ComponentRegistry.register(name, component)
            })
        }
        if (options.overrideComponents.tables) {
            Object.entries(options.overrideComponents.tables).forEach(([name, component]) => {
                ComponentRegistry.register(name, component)
            })
        }
    }

    // Registra componentes customizados globalmente no app
    if (options.customComponents) {
        Object.entries(options.customComponents).forEach(([name, component]) => {
            app.component(name, component)
        })
    }

    // Registra componentes globais do Raptor
    app.component(
        'NotificationDropdown',
        defineAsyncComponent(() => import('@raptor/components/NotificationDropdown.vue'))
    )

    // Registra formatadores personalizados
    if (options.customFormatters) {
        Object.entries(options.customFormatters).forEach(([name, func]) => {
            // Futuro: FormattersRegistry.register(name, func);
        })
    }

    app.provide('componentRegistry', ComponentRegistry)
}

/**
 * Export do plugin com tipagem explícita
 */
export const RaptorPlugin: Plugin = {
    install
}

/**
 * Export dos registries para uso direto
 */
export { ComponentRegistry }

/**
 * Export default para compatibilidade
 */
export default RaptorPlugin