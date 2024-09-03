<template>
    <div class="flex items-start gap-2 text-sm">
        <ChatBubbleOvalLeftIcon class="w-5 h-5 text-gray-400 shrink-0" />

        <div class="flex-1">
            <h2 class="font-medium">Observații</h2>

            <p v-if="observations" class="mt-2" v-text="observations" />

            <div class="flex flex-wrap gap-2 mt-4">
                <span v-for="(value, key) in info" :key="key" class="inline-flex items-center gap-1">
                    <CheckIcon v-if="value" class="w-4 h-4" />
                    <XMarkIcon v-else class="w-4 h-4" />

                    <span v-text="key" />
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ChatBubbleOvalLeftIcon } from '@heroicons/vue/20/solid';
    import { CheckIcon, XMarkIcon } from '@heroicons/vue/16/solid';

    const props = defineProps({
        observations: {
            type: String,
            default: null,
        },
        info: {
            type: Object,
            default: () => ({}),
        },
    });
</script>
