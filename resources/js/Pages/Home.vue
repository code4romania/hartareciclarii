<template>
    <PointDetails v-if="showPointDetails" :point="point" />
</template>


<script setup>
    import { computed } from 'vue';
    import DefaultLayout from '@/Layouts/DefaultLayout.vue';
    import MapLayout from '@/Layouts/MapLayout.vue';
    import PointDetails from '@/Components/PointDetails/Index.vue';

    defineOptions({
        layout: [DefaultLayout, MapLayout],
    });

    const props = defineProps({
        context: {
            type: String,
        },
        point: {
            type: Object,
        },
    });

    const point = computed(() => {
        if (props.point) {
            return props.point;
        }

        return null;
    });

    const showPointDetails = computed(() => {
        if (props.context === 'point') {
            return true;
        }

        if (props.context === 'search' && point.value !== null) {
            return true;
        }

        return false;
    });
</script>
