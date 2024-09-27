<template>
    <FormField :name="name" :label="label" :help="help" :required="required" :disabled="disabled" :errors="errors">
        <template #default="{ invalid }">
            <InputText
                :type="type"
                v-model="modelValue"
                class="w-full"
                :invalid="invalid"
                :placeholder="placeholder"
                :required="required"
                :disabled="disabled"
                :readonly="readonly"
            />
        </template>
    </FormField>
</template>

<script setup>
    import { computed } from 'vue';
    import InputText from 'primevue/inputtext';
    import FormField from '@/Components/Form/Field.vue';

    const props = defineProps({
        name: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            default: 'text',
        },
        required: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        readonly: {
            type: Boolean,
            default: false,
        },
        label: {
            type: String,
            default: null,
        },
        placeholder: {
            type: String,
            default: null,
        },
        help: {
            type: String,
            default: null,
        },
        modelValue: {
            type: [Array, String, Number],
            default: null,
        },
        errors: {
            type: Array,
            default: () => [],
        },
    });

    const emit = defineEmits(['update:modelValue']);

    const modelValue = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });
</script>
