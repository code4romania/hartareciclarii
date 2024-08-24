<template>
    <div class="flex items-start gap-2 px-6 py-4">
        <ClockIcon class="w-5 h-5 text-gray-400 shrink-0" />

        <div class="flex-1" v-text="schedule" />
    </div>
</template>

<script setup>
    import { ClockIcon } from '@heroicons/vue/24/solid';

    const props = defineProps({
        schedule: {
            type: String,
        },
    });
</script>

