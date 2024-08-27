<template>
    <FormField :name="name" :label="label" :help="help" :required="required" :disabled="disabled">
        <div class="grid gap-2">
            <label v-for="(option, index) in options" class="flex gap-x-2" :key="index">
                <input
                    type="checkbox"
                    :id="`field-${name}`"
                    :name="name"
                    class="text-blue-600 border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 my-0.5"
                    v-model="proxyChecked"
                    :value="option.value"
                    :required="required"
                    :disabled="disabled"
                />

                <span class="text-sm font-medium text-gray-700" v-text="$t(option.label)" />
            </label>
        </div>
    </FormField>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { trans } from 'laravel-vue-i18n';
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
        default: {
            type: [String, Number],
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

    const proxyChecked = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });

    // onMounted(() => {
    //     if (props.modelValue === null || props.modelValue === 0) {
    //         proxyChecked.value = props.default;
    //     }
    // });
</script>
