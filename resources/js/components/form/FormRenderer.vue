<template>
    <form class="space-y-6" @submit.prevent="handleSubmit">
        <div :class="formClasses" class="space-y-2">
            <div
                v-for="(item, index) in fields"
                :key="fieldKey(item, index)"
                :class="isSection(item) ? getColumnClasses({ name: '', columnSpan: (item as FormSection).columnSpan ?? 'full' }) : getColumnClasses(item)"
                :style="isSection(item) ? {} : getColumnStyles(item)"
                class="space-y-2"
            >
                <FieldRenderer
                    :field="item"
                    :model-value="getModelValue(item)"
                    @update:model-value="setModelValue(item, $event)"
                />
            </div>
        </div>
        <div v-if="hasFooterActions" class="flex flex-wrap justify-end gap-2">
            <ActionRenderer
                v-for="action in footerActions"
                :key="action.name"
                :action="(action as TableAction)"
                @click="(e: Event) => onFooterActionClick(action, e)"
            />
        </div>
    </form>
</template>

<script lang="ts" setup>
import { computed, reactive, ref, watch, provide } from 'vue';
import { router } from '@inertiajs/vue3';
import FieldRenderer from '@raptor/components/form/FieldRenderer.vue';
import ActionRenderer from '@raptor/components/actions/ActionRenderer.vue';
import { useGridLayout } from '@raptor/composables/useGridLayout';
import type {
    FormField,
    FormActionPayload,
    FormGridLayout,
    FormSection,
    FormFieldOrSection,
    TableAction,
} from '@raptor/types';

const props = withDefaults(
    defineProps<{
        fields: FormFieldOrSection[];
        values?: Record<string, unknown>;
        submitUrl?: string | null;
        submitMethod?: string;
        footerActions?: FormActionPayload[];
        gridLayout?: FormGridLayout | null;
    }>(),
    {
        values: () => ({}),
        submitUrl: null,
        submitMethod: 'post',
        footerActions: () => [],
        gridLayout: null,
    },
);

const formData = reactive<Record<string, unknown>>(
    JSON.parse(JSON.stringify(props.values ?? {})),
);
const isSubmitting = ref(false);

const hasFooterActions = computed(() => (props.footerActions?.length ?? 0) > 0);

const { getFormClasses, getColumnClasses, getColumnStyles } = useGridLayout();
const formClasses = computed(() =>
    getFormClasses(
        props.gridLayout?.gridColumns ?? '12',
        props.gridLayout?.gap ?? '4',
    ),
);

function isSection(item: FormFieldOrSection): item is FormSection {
    return (item as FormSection).type === 'section';
}

function fieldKey(item: FormFieldOrSection, index: number): string {
    if (isSection(item)) return `section-${(item as FormSection).name ?? index}`;
    return (item as FormField).name;
}

function getFieldValueKey(section: FormSection, field: FormField): string {
    if (section.relationship) return `${section.relationship}.${field.name}`;
    if (section.statePath) return `${section.statePath}.${field.name}`;
    return field.name;
}

function getFieldValue(section: FormSection, field: FormField): unknown {
    return getValueByPath(formData, getFieldValueKey(section, field));
}

function setFieldValue(
    section: FormSection,
    field: FormField,
    value: unknown,
): void {
    setValueByPath(formData, getFieldValueKey(section, field), value);
}

provide('formData', formData);
provide('formClasses', formClasses);
provide('getColumnClasses', getColumnClasses);
provide('getColumnStyles', getColumnStyles);
provide('getFieldValueKey', getFieldValueKey);
provide('getFieldValue', getFieldValue);
provide('setFieldValue', setFieldValue);

function getModelValue(item: FormFieldOrSection): unknown {
    if (isSection(item)) return undefined;
    return formData[(item as FormField).name];
}

function setModelValue(item: FormFieldOrSection, value: unknown): void {
    if (isSection(item)) return;
    console.log('setModelValue', item, value);
    formData[(item as FormField).name] = value;
}

function onFooterActionClick(action: FormActionPayload, event: Event) {
    if (action.type === 'submit') {
        event.preventDefault();
        handleSubmit();
    } else {
        handleFooterAction(action);
    }
}

watch(
    () => props.values,
    (newVal) => {
        deepAssign(formData, newVal ?? {});
    },
    { deep: true },
);

function getValueByPath(
    obj: Record<string, unknown>,
    path: string,
): unknown {
    return path
        .split('.')
        .reduce((o: unknown, k) => (o as Record<string, unknown>)?.[k], obj);
}

function setValueByPath(
    obj: Record<string, unknown>,
    path: string,
    value: unknown,
): void {
    const parts = path.split('.');
    if (parts.length === 1) {
        obj[path] = value;
        return;
    }
    let target: Record<string, unknown> = obj;
    for (let i = 0; i < parts.length - 1; i++) {
        const k = parts[i];
        if (
            !(k in target) ||
            typeof target[k] !== 'object' ||
            target[k] === null
        ) {
            target[k] = {};
        }
        target = target[k] as Record<string, unknown>;
    }
    target[parts[parts.length - 1]] = value;
}

function deepAssign(
    target: Record<string, unknown>,
    source: Record<string, unknown>,
): void {
    for (const key of Object.keys(source)) {
        const srcVal = source[key];
        if (
            srcVal !== null &&
            typeof srcVal === 'object' &&
            !Array.isArray(srcVal) &&
            target[key] !== null &&
            typeof target[key] === 'object' &&
            !Array.isArray(target[key])
        ) {
            deepAssign(
                target[key] as Record<string, unknown>,
                srcVal as Record<string, unknown>,
            );
        } else {
            target[key] = JSON.parse(JSON.stringify(srcVal));
        }
    }
}

function handleFooterAction(action: FormActionPayload) {
    if ((action.type === 'url' || action.type === 'cancel') && action.url) {
        if (action.inertia) {
            router.visit(action.url);
        } else {
            window.location.href = action.url;
        }
    }
}

function handleSubmit() {
    if (!props.submitUrl) return;
    isSubmitting.value = true;
    const method = (props.submitMethod ?? 'post').toLowerCase();
    const onFinish = () => {
        isSubmitting.value = false;
    };
    if (method === 'put' || method === 'patch') {
        router.put(props.submitUrl, formData as Record<string, string>, {
            onFinish,
        });
    } else {
        router.post(props.submitUrl, formData as Record<string, string>, {
            onFinish,
        });
    }
}
</script>
