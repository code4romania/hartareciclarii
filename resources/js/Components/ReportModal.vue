<template>
    <Modal :open="open" @close="close">
        <template #title>
            {{ $t('report.title') }}
        </template>

        <template #default>
            <div class=""></div>
        </template>
    </Modal>
</template>

<script setup>
    import Modal from '@/Components/Modal.vue';
    import { router, usePage } from '@inertiajs/vue3';
    import { computed } from 'vue';
    import route from '@/Helpers/useRoute.js';

    const page = usePage();

    const props = defineProps({
        point: {
            type: Object,
        },
    });

    const open = computed(() => page.props.report === true);

    const close = () => {
        const { coordinates } = route().params;

        router.get(
            route('front.map.point', {
                point: props.point,
                coordinates,
            }),
            {},
            { only: ['report'] }
        );
    };
</script>
