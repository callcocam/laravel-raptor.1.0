<?php

/**
 * Alias para compatibilidade com código antigo
 * Use as classes específicas quando possível:
 * - TextEditableColumn
 * - NumberEditableColumn
 * - EmailEditableColumn
 * - TextareaEditableColumn
 * - SelectEditableColumn
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types;

use Callcocam\LaravelRaptor\Support\Table\Columns\Types\Editable\EditableColumn as BaseEditableColumn;

// Backward compatibility alias
class_alias(BaseEditableColumn::class, EditableColumn::class);

// Re-export the base class
class EditableColumn extends BaseEditableColumn {}
