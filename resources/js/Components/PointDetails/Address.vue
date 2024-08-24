<template>
    <div class="flex items-start gap-2 px-6 py-4">
        <MapPinIcon class="w-5 h-5 text-gray-400 shrink-0" />

        <address class="flex-1 text-sm not-italic" v-text="address" />

        <button type="button" @click="copy(address)">
            <ClipboardDocumentIcon v-if="!copied || text !== address" class="w-5 h-5 text-gray-400 shrink-0" />

            <ClipboardDocumentCheckIcon v-else class="w-5 h-5 text-green-500 shrink-0" />
        </button>
    </div>
</template>

<script setup>
    import { useClipboard } from '@vueuse/core';
    import { MapPinIcon, ClipboardDocumentCheckIcon } from '@heroicons/vue/24/solid';
    import { ClipboardDocumentIcon } from '@heroicons/vue/24/outline';

    const props = defineProps({
        address: {
            type: String,
        },
    });

    const { text, copy, copied } = useClipboard({ legacy: true });
</script>

