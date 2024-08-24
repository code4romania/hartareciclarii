<template>
    <div class="flex items-start gap-2 px-6 py-2">
        <GlobeEuropeAfricaIcon class="w-5 h-5 text-gray-400 shrink-0" />

        <a
            :href="website"
            target="_blank"
            rel="noopener noreferrer"
            class="hover:underline focus:underline"
            v-text="website"
        />
    </div>
</template>

<script setup>
    import { GlobeEuropeAfricaIcon } from '@heroicons/vue/24/solid';

    const props = defineProps({
        website: {
            type: String,
            required: true,
        },
    });
</script>
