<template>
    <FormField :name="name" :label="label" :help="help" :required="required" :disabled="disabled" :errors="errors">
        <template #default="{ invalid }">
            <Textarea
                :rows="rows"
                v-model="modelValue"
                :options="options"
                optionValue="value"
                optionLabel="label"
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
    import { computed, ref } from 'vue';
    import { CheckIcon } from '@heroicons/vue/16/solid';
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
        options: {
            type: Array,
            default: () => [],
        },
        optionValueKey: {
            type: String,
            default: 'value',
        },
        optionLabelKey: {
            type: String,
            default: 'label',
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

    const getOption = (option, key) => (option.hasOwnProperty(key) ? option[key] : option);

    const options = computed(() =>
        props.options.map((option) => ({
            value: getOption(option, props.optionValueKey),
            label: getOption(option, props.optionLabelKey),
        }))
    );

    const modelValue = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });
</script>
