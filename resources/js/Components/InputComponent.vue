<template>
    <div>
        <!-- Label -->
        <label :for="id" class="block text-sm font-semibold leading-6 text-gray-900">
            <span v-if="label">{{ label }} <span v-if="isRequired" class="text-red-800">*</span> </span>
            <span v-else><slot /></span>
        </label>

        <!-- Input -->
        <input
            class="block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
            :value="modelValue"
            :type="type"
            :autocomplete="hasAutocomplete"
            v-bind="{ required: isRequired }"
            @input="$emit('update:modelValue', $event.target.value)"
            ref="input"
            :placeholder="placeholder"
            :pattern="pattern"
            :min="min"
            :max="max"
            :disabled="disabled"
            :maxlength="maxlength"
        />

        <!-- Extra -->
        <slot />

        <!-- Error -->
        <p v-show="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
<script setup>
const props = defineProps({
    modelValue: {
        type: String,
        required: true
    },
    error: {
        type: String,
    },
    label: {
        type: String,
        required: true
    },
    id: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'text'
    },
    hasAutocomplete: {
        type: String,
    },
    isRequired: {
        type: Boolean,
    },
    placeholder: {
        type: String
    },
    color:{
        type: String
    },
    pattern: {
        type: String,
        default: null
    },
    min: {
        type: Number,
        default: null
    },
    max: {
        type: Number,
        default: null
    },
    maxlength: {
        type: Number,
        default: null
    },
    disabled: {
        type: Boolean,
        default: false
    }
});
</script>
