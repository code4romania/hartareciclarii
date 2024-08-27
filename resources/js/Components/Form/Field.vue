<template>
    <div class="space-y-1">
        <label
            v-if="label"
            :for="`field-${name}`"
            class="mb-4 text-sm font-medium"
            :class="[hasErrors ? 'text-red-600' : 'text-gray-700']"
        >
            <span class="inline-block" v-text="label" />

            <span
                role="presentation"
                :title="$t('field.required')"
                class="inline-block font-bold text-red-500 ml-0.5"
                v-if="required && !disabled"
                v-text="'*'"
            />
        </label>

        <div class="relative flex flex-wrap" :class="[hasErrors ? 'border-red-600' : 'border-gray-400']">
            <slot />
        </div>

        <p v-if="help" class="text-xs text-gray-500" v-text="help" />

        <div v-if="hasErrors" class="space-y-1 text-sm text-red-600" role="alert">
            <p v-for="(message, locale) in errors" :key="locale" v-text="message" />
        </div>
    </div>
</template>

<script setup>
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';

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
        label: {
            type: String,
            default: null,
        },
    });

    const page = usePage();

    const errors = computed(() => {
        const initialErrors = page.props.errors;

        if (initialErrors.hasOwnProperty(props.name)) {
            return [initialErrors[props.name]];
        }

        return {};
    });

    const hasErrors = computed(() => Object.keys(errors.value).length > 0);
</script>
