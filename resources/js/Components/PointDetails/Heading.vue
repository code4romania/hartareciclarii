<template>
    <header class="sticky top-0 flex items-start gap-4 px-6 py-4 bg-white">
        <Icon v-if="is_sgr" :icon="`services/sgr`" class="w-8 h-8 shrink-0" />

        <Icon v-else :icon="`services/${service}`" class="w-8 h-8 shrink-0" />

        <h1
            class="flex-1 text-2xl font-bold leading-snug break-words text-neutral-900 whitespace-break-spaces"
            v-text="name"
        />

        <button type="button" class="-mr-2 text-gray-400 hover:text-gray-500 shrink-0" @click="$emit('close')">
            <XMarkIcon class="w-6 h-6" aria-hidden="true" />
        </button>
    </header>
</template>

<script setup>
    import { XMarkIcon } from '@heroicons/vue/24/solid';
    import Icon from '@/Components/Icon.vue';

    defineEmits(['close']);

    const props = defineProps({
        service: {
            type: String,
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
        is_sgr: {
            type: Boolean,
            required: false,
            default: false,
        },
    });
</script>
