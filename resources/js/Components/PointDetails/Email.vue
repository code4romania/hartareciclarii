<template>
    <div class="flex items-start gap-2 text-sm">
        <EnvelopeIcon class="w-5 h-5 text-gray-400 shrink-0" />

        <a :href="`mailto:${email}`" class="hover:underline focus:underline" v-text="email" />
    </div>
</template>

<script setup>
    import { EnvelopeIcon } from '@heroicons/vue/20/solid';

    const props = defineProps({
        email: {
            type: String,
            required: true,
        },
    });
</script>
