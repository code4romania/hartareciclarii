<template>
    <div class="space-y-1">
        <label
            v-if="label"
            :for="`field-${name}`"
            class="mb-4 text-sm font-medium"
            :class="[errors.length ? 'text-red-600' : 'text-gray-700']"
        >
            <span v-text="label" />

            <span
                role="presentation"
                :title="$t('field.required')"
                class="font-bold text-red-500 ml-0.5"
                v-if="required && !disabled"
                v-text="'*'"
            />
        </label>

        <p v-if="help && helpPosition === 'top'" class="text-sm text-gray-500" v-text="help" />

        <div class="flex flex-wrap" :class="[errors.length ? 'border-red-600' : 'border-gray-400']">
            <slot :invalid="errors.length > 0" />
        </div>

        <p v-if="help && helpPosition === 'bottom'" class="text-sm text-gray-500" v-text="help" />

        <div v-if="errors.length" class="space-y-1 text-sm text-red-600" role="alert">
            <p v-for="(error, index) in errors" :key="index" v-text="error" />
        </div>
    </div>
</template>

<script setup>
    import { computed } from 'vue';

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
        help: {
            type: String,
            default: null,
        },
        helpPosition: {
            type: String,
            default: 'bottom',
            validator: (value) => ['top', 'bottom'].includes(value),
        },
        label: {
            type: String,
            default: null,
        },
        errors: {
            type: Array,
            default: () => [],
        },
    });

    const errors = computed(() => [...new Set(props.errors.filter(Boolean))]);
</script>
