<template>
    <FormField
        :name="name"
        :label="label"
        :help="help"
        :help-position="helpPosition"
        :required="required"
        :disabled="disabled"
        :errors="errors"
    >
        <template #default="{ invalid }">
            <Textarea
                :rows="rows"
                v-model="modelValue"
                class="w-full"
                :invalid="invalid"
                :placeholder="placeholder"
                :required="required"
                :disabled="disabled"
            />
        </template>
    </FormField>
</template>

<script setup>
    import { computed } from 'vue';
    import Textarea from 'primevue/textarea';
    import FormField from '@/Components/Form/Field.vue';

    const props = defineProps({
        name: {
            type: String,
            required: true,
        },
        rows: {
            type: Number,
            default: 5,
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
        helpPosition: {
            type: String,
            default: 'bottom',
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
