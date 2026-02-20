<?php

/**
 * Editable Column Types
 *
 * Classes base e específicas para colunas editáveis inline.
 */

namespace Callcocam\LaravelRaptor\Support\Table\Columns\Types\Editable;

// Base class
export(BaseEditableColumn::class);
export(EditableColumn::class);

// Specific implementations
export(TextEditableColumn::class);
export(NumberEditableColumn::class);
export(EmailEditableColumn::class);
export(TextareaEditableColumn::class);
export(SelectEditableColumn::class);
