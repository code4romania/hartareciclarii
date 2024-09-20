<template>
    <Modal
        :dismissable="!isStep('location') && !form.wasSuccessful"
        :overlay-dismissable="false"
        form
        @submit="submit"
        :open="open"
        @open="$emit('open')"
        @close="closeModal"
    >
        <template v-if="!form.wasSuccessful" #title>
            <div v-if="isStep('location')" class="flex gap-2">
                <button type="button" @click="() => goToStep('type')">
                    <ArrowLeftIcon class="w-6 h-6 text-gray-400" />
                </button>

                <span v-text="$t('add_point.type.change_pin_location')" />
            </div>

            <span v-else v-text="$t('add_point.title')" />
        </template>

        <template #default="{ close }">
            <ThanksStep v-if="form.wasSuccessful" :form="form" :close="close" />

            <div v-else class="grid gap-4">
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
            </div>
        </template>

        <template v-if="!form.wasSuccessful" #footer="{ close }">
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

            <Modal
                v-else
                :open="showConfirmAddressOverrideModal"
                @open="showConfirmAddressOverrideModal = true"
                @close="showConfirmAddressOverrideModal = false"
            >
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

    import { ArrowLeftIcon } from '@heroicons/vue/20/solid';

    const emit = defineEmits(['open', 'close']);

    const props = defineProps({
        simple: {
            type: Boolean,
            default: false,
        },
        open: {
            type: Boolean,
            default: false,
        },
    });

    const page = usePage();

    const closeModal = () => {
        emit('close');

        setTimeout(() => {
            form.reset();
            form.wasSuccessful = false;
        }, 500);
    };

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
        materials: [],
    });

    const primaryButtonLabel = computed(
        () =>
            ({
                type: trans('action.next_step'),
                location: trans('action.save'),
                details: trans('action.next_step'),
                materials: trans('action.next_step'),
                review: trans('action.finish_steps'),
            })[form.step]
    );

    const secondaryButtonLabel = computed(
        () =>
            ({
                type: trans('action.cancel'),
                location: trans('action.reset'),
                details: trans('action.back'),
                materials: trans('action.back'),
                review: trans('action.back'),
            })[form.step]
    );

    const getFieldsByStep = () =>
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
        })[form.step];

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

            return form.transform(transform).touch(getFieldsByStep()).validate({
                onSuccess: nextStep,
            });
        }

        form.transform(transform).submit({
            onError: (error) => {
                console.error(error);
            },
        });
    };

    const isStep = (step) => form.step === step;

    const goToStep = (step) => (form.step = step);

    const previousStep = (close) => {
        if (isStep('type')) {
            return close();
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
    };

    const cancelAddressOverride = (close) => {
        close();

        form.address_override = null;

        form.reset('city', 'county');

        goToStep('type');
    };

    const confirmAddressOverride = (close) => {
        close();
        form.address = form.address_override;
        form.address_override = null;

        goToStep('type');
    };

    const showConfirmAddressOverrideModal = ref(false);
</script>
