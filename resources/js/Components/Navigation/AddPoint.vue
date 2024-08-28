<template>
    <Modal dismissable overlay-dismissable>
        <template #trigger="{ open }">
            <Button :label="$t('top_menu.add_point')" :icon="MapPinIcon" @click="open" />
        </template>

        <template #title>
            {{ $t('add_point.title') }}
        </template>

        <template #footer>
            <Button size="sm" :label="$t('common.cancel')" @click="close" />
            <Button size="sm" :label="$t('common.save')" @click="submitForm" primary />
        </template>
    </Modal>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { useForm, usePage } from '@inertiajs/vue3';
    import { trans } from 'laravel-vue-i18n';
    import route from '@/Helpers/useRoute';

    import { MapPinIcon } from '@heroicons/vue/20/solid/index.js';
    import { XMarkIcon } from '@heroicons/vue/20/solid';

    import Button from '@/Components/Button.vue';
    import SearchList from '@/Components/AddPoint/SearchList.vue';
    import SearchWithMap from '@/Components/AddPoint/SearchWithMap.vue';
    import Modal from '@/Components/Modal.vue';

    const page = usePage();
    const currentStep = ref(1);

    const errors = computed(() => page.props.errors || {});

    const unknownAdministration = ref(false);

    const form = useForm({
        address: null,
        service_type: null,
        lat: null,
        lng: null,
        point_type: null,
        administrated_by: null,
    });

    const serviceTypes = computed(() =>
        page.props.service_types.map((service) => ({
            label: service.name,
            value: service.id,
        }))
    );

    const pointTypes = computed(() => {
        if (!form.service_type) {
            return [];
        }

        return page.props.service_types
            .find((serviceType) => serviceType.id === form.service_type)
            .point_types.map((pointType) => ({
                label: pointType.name,
                value: pointType.id,
            }));
    });

    const setPoint = (e) => {
        form.lat = e.lat;
        form.lng = e.lng;
        form.address = e.address;
    };

    const submitForm = (e) => {
        form.post(route('point.store'));

        if (!validateInputs()) {
            return;
        }

        if (currentStep.value !== 3) {
            currentStep.value = currentStep.value + 1;
            return;
        }
    };

    const validateInputs = () => {
        if (currentStep.value === 1) {
            if (form.service_type === '') {
                errors.value.service_type = trans('add_point.service_type_required');
            }
            if (form.address === '') {
                errors.value.address = trans('add_point.address_required');
            }
        }

        return Object.keys(errors.value).length === 0;
    };
</script>
