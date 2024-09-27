<template>
    <FormField :name="name" :label="label" :help="help" :required="required" :disabled="disabled">
        <AutoComplete v-model="modelValue" class="w-full" @complete="complete" fluid />
    </FormField>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import AutoComplete from 'primevue/autocomplete';
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
        help: {
            type: String,
            default: null,
        },
        modelValue: {
            type: String,
            default: null,
        },
        complete: {
            type: Function,
            default: () => {},
        },
    });

    const emit = defineEmits(['update:modelValue']);

    const modelValue = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });
</script>
