/**
 * ComponentRegistry - Gerencia registro de componentes de tabela (colunas) e outros.
 *
 * Usado por ColumnRenderer para resolver o componente da célula pelo nome (ex: 'text-table-column').
 *
 * @example
 * ComponentRegistry.register('text-table-column', TextTableColumn)
 * const component = ComponentRegistry.get('text-table-column')
 */

import type { Component } from 'vue'

type ComponentMap = Record<string, Component>

class ComponentRegistryClass {
  private components: ComponentMap = {}

  register(name: string, component: Component): void {
    this.components[name] = component
  }

  registerBulk(components: ComponentMap): void {
    Object.entries(components).forEach(([name, component]) => {
      this.register(name, component)
    })
  }

  get(name: string): Component | undefined {
    return this.components[name]
  }

  has(name: string): boolean {
    return name in this.components
  }
}

// Exporta instância singleton
export const ComponentRegistry = new ComponentRegistryClass()

// Exporta também a classe para testes
export { ComponentRegistryClass }

// Export default para compatibilidade
export default ComponentRegistry
