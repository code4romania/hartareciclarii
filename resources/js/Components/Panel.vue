<template>
    <component :is="as" class="max-w-3xl overflow-hidden bg-white rounded-lg shadow" @submit="$emit('submit', $event)">
        <div class="px-4 py-5 sm:p-6">
            <slot />
        </div>
        <div
            v-if="$slots.footer"
            class="flex flex-col-reverse justify-end gap-4 px-4 py-4 bg-gray-50 sm:px-6 sm:flex-row"
        >
            <slot name="footer" />
        </div>
    </component>
</template>

<script setup>
    defineProps({
        as: {
            type: [Object, String],
            default: 'div',
        },
    });

    defineEmits(['submit']);
</script>
