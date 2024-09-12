<template>
    <Modal :open="open" @close="close">
        <template #title>
            {{ $t('report.title') }}
        </template>

        <template #default>
            <RadioGroup
                name="problem_type"
                :label="$t('report.type')"
                :options="$page.props.problem_types"
                option-label-key="name"
                option-value-key="id"
            />
        </template>
    </Modal>
</template>

<script setup>
    import Modal from '@/Components/Modal.vue';
    import { router, usePage } from '@inertiajs/vue3';
    import { computed } from 'vue';
    import route from '@/Helpers/useRoute.js';
    import RadioGroup from '@/Components/Form/RadioGroup.vue';

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
