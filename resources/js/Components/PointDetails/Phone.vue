<template>
    <div class="flex items-start gap-2 px-6 py-2">
        <PhoneIcon class="w-5 h-5 text-gray-400 shrink-0" />

        <a :href="`tel:${phone}`" class="hover:underline focus:underline" v-text="phone" />
    </div>
</template>

<script setup>
    import { defineProps } from 'vue';
    import { PhoneIcon } from '@heroicons/vue/24/solid';

    const props = defineProps({
        phone: {
            type: String,
            required: true,
        },
    });
</script>
