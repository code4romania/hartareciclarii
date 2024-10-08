<template>
    <div class="p-4 rounded-md bg-green-50" :class="color">
        <div class="flex items-center gap-3 text-sm">
            <Component :is="icon" class="w-5 h-5 shrink-0" :class="iconColor" aria-hidden="true" />

            <p v-text="message" />

            <slot name="action" />
        </div>
    </div>
</template>

<script setup>
    import { computed } from 'vue';

    /** Import plugins. */
    import { CheckCircleIcon, ExclamationTriangleIcon, ExclamationCircleIcon } from '@heroicons/vue/20/solid';

    /** Component props. */
    const props = defineProps({
        type: {
            type: String,
            required: true,
            validator: (type) => ['success', 'error', 'warning'].includes(type),
        },
        message: {
            type: String,
            required: true,
        },
    });

    const icon = computed(() => {
        if (props.type === 'warning') {
            return ExclamationTriangleIcon;
        }

        if (props.type === 'error') {
            return ExclamationCircleIcon;
        }

        return CheckCircleIcon;
    });

    const iconColor = computed(() => {
        if (props.type === 'warning') {
            return 'text-yellow-500';
        }

        if (props.type === 'error') {
            return 'text-red-500';
        }

        return 'text-green-500';
    });

    const color = computed(() => {
        if (props.type === 'warning') {
            return 'bg-yellow-50 text-yellow-800';
        }

        if (props.type === 'error') {
            return 'bg-red-50 text-red-800';
        }

        return 'bg-green-50 text-green-800';
    });
</script>
