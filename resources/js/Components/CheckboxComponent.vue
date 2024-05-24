<template>
    <div class="relative flex items-start">
        <div class="flex h-6 items-center">
            <input
                :id="id"
                :aria-describedby="description"
                :name="label"
                type="checkbox"
                :value="modelValue"
                v-model="proxyChecked"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
        </div>
        <div class="ml-3 text-sm leading-6">
            <label for="comments" class="font-medium text-gray-900">{{ label }}
                <span v-if="isRequired" class="text-red-800">*</span>
            </label>
            <span id="comments-description" class="text-gray-500" v-html="description"/>
        </div>
        <p v-show="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
<script setup>

import {computed} from "vue";

const emit = defineEmits(['update:checked']);

const props = defineProps({
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
    isRequired: {
        type: Boolean,
    },
    description: {
        type: String,
        default: ''
    },
    checked: {
        type: Boolean,
        default: false
    }

});
const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>
