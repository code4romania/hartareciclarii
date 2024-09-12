<template>
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <p class="mb-5 text-gray-500" v-text="$t('report.footer')" />

        <Link :href="route('front.map.report', { point, coordinates })" :only="['report', 'problem_types']">
            <span v-text="$t('point.action.report')" />
        </Link>
    </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';
    import route from '@/Helpers/useRoute.js';

    const { coordinates } = route().params;

    const props = defineProps({
        point: {
            type: Object,
            required: true,
        },
    });
</script>
