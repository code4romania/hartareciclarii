<template>
    <FormField :name="name" :label="label" :help="help" :required="required" :disabled="disabled" :errors="errors">
        <template #default="{ invalid }">
            <Select
                v-model="modelValue"
                :options="options"
                class="w-full"
                :placeholder="placeholder"
                :required="required"
                :disabled="disabled"
                :errors="errors"
                :invalid="invalid"
                :optionValue="optionValue"
                :optionLabel="optionLabel"
            />
        </template>
    </FormField>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import Select from 'primevue/select';
    import FormField from '@/Components/Form/Field.vue';

    const props = defineProps({
        name: {
            type: String,
            required: true,
        },
        required: {
            type: Boolean,
            default: false,
        },
        disabled: {
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
        options: {
            type: Array,
            default: () => [],
        },
        optionValue: {
            type: String,
            default: 'value',
        },
        optionLabel: {
            type: String,
            default: 'label',
        },
        modelValue: {
            type: [Array, String, Number, Boolean],
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
