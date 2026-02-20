# Editable Column Components

Componentes autossuficientes para edição inline de campos nas tabelas usando **shadcn-vue**.

## 📁 Estrutura

```
editable/
├── EditableTextInput.vue      (Input text, email, number)
├── EditableTextarea.vue       (Textarea)
├── EditableSelect.vue         (Select dropdown)
├── index.ts                   (Exports)
└── README.md                  (Este arquivo)
```

## 🎯 Uso

Cada componente é autossuficiente e recebe `record` e `column` como props (igual ao TextTableColumn).

### Props Comuns

```typescript
{
  record: Record<string, unknown>  // O registro da tabela
  column: {
    name: string                   // Nome do campo
    label?: string                 // Label/placeholder
    icon?: string                  // Ícone dinâmico
    prefix?: string                // Texto antes do valor
    suffix?: string                // Texto depois do valor
    isBadge?: boolean              // Exibir como badge
    color?: string                 // Cor do badge
    limit?: number                 // Truncar texto
    placeholder?: string           // Placeholder do input
  }
}
```

### EditableTextInput.vue

Para campos text, email e number.

```vue
<EditableTextInput
  :record="record"
  :column="{
    name: 'email',
    label: 'E-mail',
    inputType: 'email',
    icon: 'mail'
  }"
/>
```

### EditableTextarea.vue

Para campos de texto longo.

```vue
<EditableTextarea
  :record="record"
  :column="{
    name: 'description',
    label: 'Descrição',
    rows: 4
  }"
/>
```

### EditableSelect.vue

Para campos com opções.

```vue
<EditableSelect
  :record="record"
  :column="{
    name: 'status',
    label: 'Status',
    options: {
      'draft': 'Rascunho',
      'published': 'Publicado'
    },
    isBadge: true,
    color: 'success'
  }"
/>
```

## 🔄 Comportamento

- **Sempre Editável**: O campo sempre aparece como input, sem modo display
- **Auto-save**: 
  - TextInput/Textarea: Salva ao perder foco (blur) ou Enter
  - Select: Salva automaticamente ao selecionar
- **Loading**: Mostra spinner durante salvamento
- **Erros**: Exibe mensagem inline em vermelho
- **Rota**: Usa `/{resource}/{id}/update-field` (igual CallbackAction)

## 🚀 Uso

### Backend (PHP)

```php
use Callcocam\LaravelRaptor\Support\Table\Columns\Types\Editable\NumberEditableColumn;

NumberEditableColumn::make('stock')
    ->label('Estoque')
    ->rules(['numeric', 'min:0', 'max:999999'])
    ->updateUsing(function (Product $model, $value) {
        $model->stock = $value;
        $model->save();
    })
```

### Componente é registrado automaticamente

O componente `EditableTableColumn.vue` é automaticamente:
1. Registrado no `raptor/index.ts`
2. Usado quando coluna tem tipo `editable-table-column`

## 🎨 Features

- ✅ Modular e reutilizável
- ✅ shadcn-vue components
- ✅ Keyboard shortcuts (Enter=save, Esc=cancel)
- ✅ Loading states e spinners
- ✅ Error messages inline
- ✅ Hover effects
- ✅ Support a nested properties (dot notation)
- ✅ TypeScript
- ✅ Dark mode support

## 🔧 Customização

### Estender EditableTextInput

```vue
<script setup lang="ts">
import EditableTextInput from '@raptor/components/table/columns/editable/EditableTextInput.vue'

// Use com props customizadas
</script>

<template>
  <EditableTextInput
    v-model="value"
    input-type="email"
    placeholder="seu@email.com"
    @save="onSave"
    @cancel="onCancel"
  />
</template>
```

### Criar novo tipo de input

```vue
<!-- CustomEditableInput.vue -->
<template>
  <div class="custom-input">
    <!-- your custom input -->
  </div>
</template>

<script setup lang="ts">
defineEmits(['save', 'cancel', 'update:modelValue'])
defineProps(['modelValue', 'disabled'])
</script>
```

Então adicione no EditableTableColumn.vue:
```vue
<CustomEditableInput
  v-else-if="column.inputType === 'custom'"
  v-model="editValue"
  @save="saveEdit"
  @cancel="cancelEdit"
/>
```

## 📝 Notas

- Cada componente tem uma responsabilidade única
- Baixo acoplamento entre componentes
- Fácil de testar e estender
- Segue padrões de Vue 3 Composition API
- Usa shadcn-vue para consistência visual

## 🐛 Troubleshooting

### Select não atualiza
- Certifique-se de passar `options` como Record com valores strings
- Exemplo: `{ 'active': 'Ativo', 'inactive': 'Inativo' }`

### Validação não funciona
- Validação é feita no backend
- Use `rules()` no PHP com array de strings
- Exemplo: `rules(['required', 'numeric', 'min:0'])`

### Keyboard shortcuts não funcionam
- EditableTextInput trata Enter e Esc
- EditableTextarea trata apenas Esc
- SelectEditableSelect salva automaticamente ao mudar

---

**Sistema criado para ser profissional, modular e fácil de manter.** ✨
