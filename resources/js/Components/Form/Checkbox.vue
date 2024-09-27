<template>
    <label
        class="relative flex gap-x-2"
        :class="{
            'cursor-default': props.disabled,
            'cursor-pointer': !props.disabled,
        }"
    >
        <input
            type="checkbox"
            class="absolute top-0 left-0 z-10 w-full h-full p-0 m-0 border border-gray-300 rounded outline-none opacity-0 appearance-none cursor-pointer peer disabled:cursor-default"
            v-model="modelValue"
            :value="value"
            :required="required"
            :disabled="disabled"
        />

        <div
            class="flex items-center justify-center w-4 h-4 my-0.5 border border-gray-300 bg-white text-white rounded peer-checked:border-transparent peer-disabled:bg-gray-400 peer-disabled:select-none peer-disabled:pointer-events-none peer-disabled:cursor-default peer-focus-visible:z-10 peer-focus-visible:outline-none peer-focus-visible:outline-offset-0 peer-focus-visible:ring-1 peer-focus-visible:ring-offset-2 peer-focus-visible:ring-primary-500 peer-checked:ring-primary-700 peer-checked:bg-primary-700"
        >
            <CheckIcon class="w-full h-full" />
        </div>

        <span v-if="label" class="text-sm font-medium text-gray-700" v-text="label" />
    </label>
</template>

<script setup>
    import { computed } from 'vue';
    import { CheckIcon } from '@heroicons/vue/16/solid';

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
            default: null,
        },
    });

    const modelValue = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });
</script>
