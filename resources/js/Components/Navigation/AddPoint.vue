<template>
    <Modal dismissable form @submit="submit">
        <template #trigger="{ open }">
            <Button :label="$t('top_menu.add_point')" :icon="MapPinIcon" @click="open" />
        </template>

        <template #title>
            {{ $t('add_point.title') }}
        </template>

        <div class="grid gap-4">
            <TypeStep
                v-if="isStep('type')"
                :form="form"
                :serviceTypes="serviceTypes"
                @changePinLocation="() => goToStep('location')"
            />

            <LocationStep v-if="isStep('location')" :form="form" />

            <DetailsStep v-if="isStep('details')" :form="form" :serviceType="serviceType" :pointTypes="pointTypes" />

            <MaterialsStep v-if="isStep('materials')" :form="form" />

            <ReviewStep v-if="isStep('review')" :form="form" :serviceType="serviceType" :pointType="pointType" />
        </div>

        <template #footer="{ close }">
            <Button
                size="sm"
                :label="secondaryButtonLabel"
                @click="() => previousStep(close)"
                :disabled="form.validating || form.processing"
            />

            <Button
                size="sm"
                :label="primaryButtonLabel"
                type="submit"
                :disabled="form.validating || form.processing"
                primary
            />
        </template>
    </Modal>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { useForm } from 'laravel-precognition-vue-inertia';

    import { trans } from 'laravel-vue-i18n';
    import route from '@/Helpers/useRoute';

    import Button from '@/Components/Button.vue';

    import Modal from '@/Components/Modal.vue';
    import TypeStep from '@/Components/AddPointSteps/Type.vue';
    import LocationStep from '@/Components/AddPointSteps/Location.vue';
    import DetailsStep from '@/Components/AddPointSteps/Details.vue';
    import MaterialsStep from '@/Components/AddPointSteps/Materials.vue';
    import ReviewStep from '@/Components/AddPointSteps/Review.vue';

    const page = usePage();

    const errors = computed(() => page.props.errors || {});

    const unknownAdministration = ref(false);

    const steps = ['type', 'location', 'details', 'materials', 'review'];

    const form = useForm('post', route('front.point.submit'), {
        step: steps[0],

        // step 1: Type
        service_type: null,

        // step 1 & 2: Location
        address: null,
        location: {
            lat: null,
            lng: null,
        },

        // step 3: Details
        point_type: null,
        business_name: null,
        administered_by: null,
        administered_by_unknown: false,
        offers_money: null,
        offers_vouchers: null,
        offers_transport: null,
        free_of_charge: null,
        schedule: null,
        schedule_unknown: false,
        website: null,
        email: null,
        phone: null,
        observations: null,

        // step 4: Materials
        materials: {},
    });

    const primaryButtonLabel = computed(
        () =>
            ({
                type: trans('add_point.action.next_step'),
                location: trans('add_point.action.save'),
                details: trans('add_point.action.next_step'),
                materials: trans('add_point.action.next_step'),
                review: trans('add_point.action.finish_steps'),
            }[form.step])
    );

    const secondaryButtonLabel = computed(
        () =>
            ({
                type: trans('add_point.action.cancel'),
                location: trans('add_point.action.reset'),
                details: trans('add_point.action.back'),
                materials: trans('add_point.action.back'),
                review: trans('add_point.action.back'),
            }[form.step])
    );

    const getFieldsByStep = (step) =>
        ({
            type: [
                //
                'service_type',
                'address',
                'location.lat',
                'location.lng',
            ],
            location: [
                //
                'address',
                'location.lat',
                'location.lng',
            ],
            details: [
                'point_type',
                'business_name',
                'administered_by',
                'administered_by_unknown',
                'offers_money',
                'offers_vouchers',
                'offers_transport',
                'free_of_charge',
                'schedule',
                'schedule_unknown',
                'website',
                'email',
                'phone',
                'observations',
            ],
            materials: ['materials'],
        }[step]);

    const serviceTypes = computed(() =>
        page.props.service_types.map((service) => ({
            label: service.name,
            value: service.id,
        }))
    );

    const serviceType = computed(() => {
        if (!form.service_type) {
            return null;
        }

        return page.props.service_types.find((serviceType) => serviceType.id === form.service_type);
    });

    const pointType = computed(() => {
        if (!form.point_type) {
            return null;
        }

        return serviceType.value.point_types.find((pointType) => pointType.id === form.point_type);
    });

    const submit = () => {
        if (!isStep('review')) {
            return form.touch(getFieldsByStep(form.step)).validate({
                onSuccess: (response) => {
                    console.log(response);

                    nextStep();
                },
            });
        }

        form.submit({
            preserveState: true,
            preserveScroll: true,
            onError: (error) => {
                console.log(error);
            },
        });
    };

    const isStep = (step) => form.step === step;

    const goToStep = (step) => (form.step = step);

    const previousStep = (close) => {
        if (isStep('type')) {
            close();
            form.reset();
            return;
        }

        if (isStep('location')) {
            return goToStep('type');
        }

        if (isStep('details')) {
            return goToStep('type');
        }

        if (isStep('materials')) {
            return goToStep('details');
        }

        if (isStep('review')) {
            return goToStep('materials');
        }
    };

    const nextStep = () => {
        if (isStep('type')) {
            return goToStep('details');
        }

        if (isStep('location')) {
            return goToStep('details');
        }

        if (isStep('details')) {
            return goToStep('materials');
        }

        if (isStep('materials')) {
            return goToStep('review');
        }
    };
</script>
