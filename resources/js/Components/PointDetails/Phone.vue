<template>
    <div class="flex items-start gap-2 text-sm">
        <PhoneIcon class="w-5 h-5 text-gray-400 shrink-0" />

        <a :href="`tel:${phone}`" class="hover:underline focus:underline" v-text="phone" />
    </div>
</template>

<script setup>
    import { PhoneIcon } from '@heroicons/vue/20/solid';

    const props = defineProps({
        phone: {
            type: String,
            required: true,
        },
    });
</script>
