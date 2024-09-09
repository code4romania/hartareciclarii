<template>
    <Modal :dismissable="!isStep('location') && !isStep('thanks')" form @submit="submit">
        <template #trigger="{ open }">
            <Button :label="$t('top_menu.add_point')" :icon="MapPinIcon" :simple="simple" @click.bubble="open" />
        </template>

        <template v-if="!isStep('thanks')" #title>
            <template v-if="isStep('location')">
                <div class="flex gap-2">
                    <button type="button" @click="() => goToStep('type')">
                        <ArrowLeftIcon class="w-6 h-6 text-gray-400" />
                    </button>

                    <span v-text="$t('add_point.type.change_pin_location')" />
                </div>
            </template>

            <span v-else v-text="$t('add_point.title')" />
        </template>

        <template #default="{ close }">
            <div class="grid gap-4">
                <TypeStep
                    v-if="isStep('type')"
                    :form="form"
                    :serviceTypes="serviceTypes"
                    @changePinLocation="changePinLocation"
                />

                <LocationStep v-if="isStep('location')" :form="form" />

                <DetailsStep v-if="isStep('details')" :form="form" :serviceType="serviceType" />

                <MaterialsStep v-if="isStep('materials')" :form="form" />

                <ReviewStep v-if="isStep('review')" :form="form" :serviceType="serviceType" :pointType="pointType" />

                <ThanksStep v-if="isStep('thanks')" :close="close" />
            </div>
        </template>

        <template v-if="!isStep('thanks')" #footer="{ close }">
            <Button
                :label="secondaryButtonLabel"
                @click="() => previousStep(close)"
                :disabled="form.validating || form.processing"
                size="sm"
            />

            <Button
                v-if="!isStep('location') || form.address_override === null"
                type="submit"
                :label="primaryButtonLabel"
                :disabled="form.validating || form.processing"
                size="sm"
                primary
            />

            <Modal v-else>
                <template #trigger="{ open }">
                    <Button
                        :label="primaryButtonLabel"
                        @click="open"
                        :disabled="form.validating || form.processing"
                        size="sm"
                        primary
                    />
                </template>

                <template #title>
                    <span v-text="$t('add_point.location.alert.title')" />
                </template>

                <p
                    class="py-3 text-sm"
                    v-html="
                        $t('add_point.location.alert.body', {
                            old: form.address || '',
                            new: form.address_override,
                        })
                    "
                />

                <template #footer="{ close }">
                    <Button
                        :label="$t('add_point.location.alert.keep_old')"
                        @click="cancelAddressOverride(close)"
                        size="sm"
                    />

                    <Button
                        :label="$t('add_point.location.alert.update_address')"
                        @click="confirmAddressOverride(close)"
                        size="sm"
                        primary
                    />
                </template>
            </Modal>
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
    import ThanksStep from '@/Components/AddPointSteps/Thanks.vue';

    import { ArrowLeftIcon, MapPinIcon } from '@heroicons/vue/20/solid';

    const props = defineProps({
        simple: {
            type: Boolean,
            default: false,
        },
    });

    const page = usePage();

    const errors = computed(() => page.props.errors || {});

    // const steps = ['type', 'location', 'details', 'materials', 'review'];

    const originalLocation = ref({
        lat: null,
        lng: null,
    });

    const form = useForm('post', route('front.submit.point'), {
        step: 'type',

        // step 1: Type
        service_type_id: null,

        // step 1 & 2: Location
        address: null,
        address_override: null,
        location: {
            lat: null,
            lng: null,
        },
        city: null,
        county: null,

        // step 3: Details
        point_type_id: null,
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
        images: [],

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
            })[form.step]
    );

    const secondaryButtonLabel = computed(
        () =>
            ({
                type: trans('add_point.action.cancel'),
                location: trans('add_point.action.reset'),
                details: trans('add_point.action.back'),
                materials: trans('add_point.action.back'),
                review: trans('add_point.action.back'),
            })[form.step]
    );

    const getFieldsByStep = (step) =>
        ({
            type: [
                //
                'service_type_id',
                'address',
                'city',
                'county',
                'location.lat',
                'location.lng',
            ],
            // location: [
            //     //
            //     'address',
            //     'location.lat',
            //     'location.lng',
            // ],
            details: [
                'point_type_id',
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
                'images',
            ],
            materials: ['materials'],
        })[step];

    const serviceTypes = computed(() =>
        page.props.service_types.map((service) => ({
            label: service.name,
            value: service.id,
        }))
    );

    const serviceType = computed(() => {
        if (!form.service_type_id) {
            return null;
        }

        return page.props.service_types.find((serviceType) => serviceType.id === form.service_type_id);
    });

    const pointType = computed(() => {
        if (!form.point_type_id) {
            return null;
        }

        return serviceType.value.point_types.find((pointType) => pointType.id === form.point_type_id);
    });

    const changePinLocation = () => {
        originalLocation.value.lat = form.location.lat;
        originalLocation.value.lng = form.location.lng;

        goToStep('location');
    };

    const resetLocation = () => {
        form.location.lat = originalLocation.value.lat;
        form.location.lng = originalLocation.value.lng;

        originalLocation.value.lat = null;
        originalLocation.value.lng = null;
    };

    const transform = (data) => {
        Object.entries(data).forEach(([key, value]) => {
            /**
             * Ensure this matches Laravel's own boolean validator.
             *
             * @see https://laravel.com/docs/11.x/validation#rule-boolean
             */
            if (typeof value === 'boolean') {
                data[key] = value ? 1 : 0;
            }
        });

        data.images = data.images.map((image) => image.uuid);

        data.materials = Object.keys(data.materials).filter((key) => !key.startsWith('cat-'));

        return data;
    };

    const submit = () => {
        if (isStep('location')) {
            return nextStep();
        }

        if (!isStep('review')) {
            if (isStep('details')) {
                form.validateFiles();
            }

            return form.transform(transform).touch(getFieldsByStep(form.step)).validate({
                onSuccess: nextStep,
            });
        }

        form.transform(transform).submit({
            preserveState: true,
            preserveScroll: true,
            onSuccess: nextStep,
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
            resetLocation();
            return goToStep('type');
        }

        if (isStep('details')) {
            return goToStep('type');
        }

        if (isStep('materials')) {
            return goToStep('details');
        }

        if (isStep('review')) {
            if (!serviceType.value.can.collect_materials) {
                return goToStep('details');
            }

            return goToStep('materials');
        }
    };

    const nextStep = (event) => {
        if (isStep('type')) {
            return goToStep('details');
        }

        if (isStep('location')) {
            return goToStep('type');
        }

        if (isStep('details')) {
            if (!serviceType.value.can.collect_materials) {
                return goToStep('review');
            }

            return goToStep('materials');
        }

        if (isStep('materials')) {
            return goToStep('review');
        }

        if (isStep('review')) {
            return goToStep('thanks');
        }
    };

    const cancelAddressOverride = (close) => {
        close();

        form.address_override = null;

        goToStep('type');
    };

    const confirmAddressOverride = (close) => {
        close();
        form.address = form.address_override;
        form.address_override = null;

        goToStep('type');
    };
</script>
