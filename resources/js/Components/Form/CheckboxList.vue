<template>
    <FormField :name="name" :label="label" :help="help" :required="required" :disabled="disabled">
        <div class="grid gap-2">
            <Checkbox
                v-for="(option, index) in options"
                :key="index"
                class="flex gap-x-2"
                v-model="modelValue"
                :value="option.value"
                :label="option.label"
                :required="required"
                :disabled="disabled"
            />
        </div>
    </FormField>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { CheckIcon } from '@heroicons/vue/16/solid';
    import Checkbox from '@/Components/Form/Checkbox.vue';
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
            type: Array,
            default: [],
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
