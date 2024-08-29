<template>
    <Modal dismissable form @submit="submit">
        <template #trigger="{ open }">
            <Button :label="$t('top_menu.add_point')" :icon="MapPinIcon" @click="open" />
        </template>

        <template #title>
            {{ $t('add_point.title') }}
        </template>

        <div class="grid gap-4">
            <Step1 v-if="form.step === 1" :form="form" :serviceTypes="serviceTypes" />
            <Step2 v-if="form.step === 2" :form="form" :pointTypes="pointTypes" />
        </div>

        <template #footer="{ close }">
            <template v-if="form.step === 1">
                <Button size="sm" :label="$t('add_point.cancel')" @click="close" :disabled="form.processing" />
                <Button
                    size="sm"
                    :label="$t('add_point.next_step')"
                    type="submit"
                    :disabled="form.processing"
                    primary
                />
            </template>

            <template v-else>
                <Button size="sm" :label="$t('add_point.back')" @click="previousStep" :disabled="form.processing" />
                <Button
                    size="sm"
                    :label="$t('add_point.finish_steps')"
                    type="submit"
                    :disabled="form.processing"
                    primary
                />
            </template>
        </template>
    </Modal>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { useForm } from 'laravel-precognition-vue-inertia';

    import { trans } from 'laravel-vue-i18n';
    import route from '@/Helpers/useRoute';

    import { MapPinIcon } from '@heroicons/vue/20/solid/index.js';

    import Button from '@/Components/Button.vue';

    import Modal from '@/Components/Modal.vue';
    import Step1 from '@/Components/AddPoint/Step1.vue';
    import Step2 from '@/Components/AddPoint/Step2.vue';

    const page = usePage();

    const errors = computed(() => page.props.errors || {});

    const unknownAdministration = ref(false);

    const form = useForm('post', route('front.point.submit'), {
        step: 1,

        // step1
        service_type: null,
        address: null,
        location: {
            lat: null,
            lng: null,
        },

        // step2
        point_type: null,
        administrated_by: null,
    });

    const getFieldsByStep = (step) =>
        ({
            1: ['service_type', 'address', 'location.lat', 'location.lng'],
            2: ['point_type', 'administrated_by'],
        }[step]);

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

    const submit = () => {
        form.touch(getFieldsByStep(form.step)).validate({
            onSuccess: (response) => {
                console.log(response);

                nextStep();
            },
        });
        return;
        form.submit({
            preserveState: true,
            preserveScroll: true,
            onError: (error) => {
                console.log(error);
            },
        });

        // if (!validateInputs()) {
        //     return;
        // }

        // if (form.step !== 3) {
        //     form.step = form.step + 1;
        //     return;
        // }
    };

    const previousStep = () => {
        form.step = Math.min(1, form.step - 1);
    };
    const nextStep = () => {
        form.step = Math.max(2, form.step + 1);
    };
</script>
