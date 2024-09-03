<template>
    <div class="flex items-start gap-2 text-sm">
        <ClockIcon class="w-5 h-5 text-gray-400 shrink-0" />

        <div class="flex-1" v-text="schedule" />
    </div>
</template>

<script setup>
    import { ClockIcon } from '@heroicons/vue/20/solid';

    const props = defineProps({
        schedule: {
            type: String,
        },
    });
</script>

