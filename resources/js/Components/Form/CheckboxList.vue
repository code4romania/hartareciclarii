<template>
    <div class="flex flex-wrap gap-4">
        <label v-for="(option, index) in options" class="flex gap-x-2" :key="index">
            <input
                type="checkbox"
                :name="name"
                class="text-blue-600 border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 my-0.5"
                v-model="proxyChecked"
                :value="option.value"
                :required="required"
                :disabled="disabled"
                :autofocus="autofocus"
            />

            <span class="text-sm font-medium text-gray-700" v-text="option.label" />
        </label>
    </div>
</template>

<script setup>
    import { computed, onMounted } from 'vue';

    const props = defineProps({
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
        default: {
            type: [String, Number],
            default: null,
        },
    });

    const emit = defineEmits(['update:modelValue']);

    const proxyChecked = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });

    onMounted(() => {
        if (props.modelValue === null || props.modelValue === 0) {
            proxyChecked.value = props.default;
        }
    });
</script>
