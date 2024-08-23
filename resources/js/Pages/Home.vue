<template>
    <PointDetails v-if="type === 'point'" :point="point" />
</template>


<script setup>
    import DefaultLayout from '@/Layouts/DefaultLayout.vue';
    import MapLayout from '@/Layouts/MapLayout.vue';
    import PointDetails from '@/Components/PointDetails/Index.vue';

    defineOptions({
        layout: [DefaultLayout, MapLayout],
    });

    const props = defineProps({
        type: {
            type: String,
        },
        point: {
            type: Object,
        },
    });
</script>
