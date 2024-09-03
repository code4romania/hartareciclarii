<template>
    <label class="flex gap-x-2">
        <Checkbox
            v-model="modelValue"
            :value="value"
            :binary="value === null"
            :required="required"
            :disabled="disabled"
        >
            <template #icon="{ checked }">
                <CheckIcon v-if="checked" />
            </template>
        </Checkbox>

        <span v-if="label" class="text-sm font-medium text-gray-700" v-text="label" />
    </label>
</template>

<script setup>
    import { computed } from 'vue';
    import { CheckIcon } from '@heroicons/vue/16/solid';
    import Checkbox from 'primevue/checkbox';

    const emit = defineEmits(['update:modelValue']);

    const props = defineProps({
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
        value: {
            type: [String, Number, Boolean],
            default: null,
        },
        modelValue: {
            // type: [String, Number, Boolean, Array, Objec],
            default: null,
        },
    });

    const modelValue = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });
</script>
