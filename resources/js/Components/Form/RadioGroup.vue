<template>
    <FormField :name="name" :label="label" :help="help" :required="required" :disabled="disabled">
        <div class="grid gap-2">
            <label v-for="(option, index) in options" :key="index" class="flex items-center gap-2">
                <input
                    type="radio"
                    v-model="modelValue"
                    :name="name"
                    :value="option.value"
                    :required="required"
                    :disabled="disabled"
                />
                <span class="text-sm font-medium text-gray-700" v-text="option.label" />
            </label>
        </div>
    </FormField>
</template>

<script setup>
    import { computed } from 'vue';
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
            type: [Number, String, Boolean],
            default: null,
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
