<template>
    <div class="flex items-start gap-2 text-sm">
        <GlobeEuropeAfricaIcon class="w-5 h-5 text-gray-400 shrink-0" />

        <a
            :href="website"
            target="_blank"
            rel="noopener noreferrer"
            class="truncate text-ellipsis hover:underline focus:underline"
            v-text="website"
        />
    </div>
</template>

<script setup>
    import { GlobeEuropeAfricaIcon } from '@heroicons/vue/20/solid';

    const props = defineProps({
        website: {
            type: String,
            required: true,
        },
    });
</script>
