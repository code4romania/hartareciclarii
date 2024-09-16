<template>
    <Modal @submit="submit" :open="open" @close="close" form>
        <template #title>
            <div v-if="isStep('changePinLocation')" class="flex gap-2">
                <button type="button" @click="previousStep">
                    <ArrowLeftIcon class="w-6 h-6 text-gray-400" />
                </button>

                <span v-text="$t('add_point.type.change_pin_location')" />
            </div>

            <span v-else v-text="$t('report.title')" />
        </template>

        <template #default>
            <div class="grid gap-4">
                <ProblemTypeStep v-if="isStep('type')" :form="form" />

                <AddressStep
                    v-if="isStep('address')"
                    :form="form"
                    :problem-type="problemType"
                    @changePinLocation="changePinLocation"
                />

                <LocationStep
                    v-if="isStep('location')"
                    :form="form"
                    :problem-type="problemType"
                    @changePinLocation="changePinLocation"
                />

                <ChangePinLocationStep v-if="isStep('changePinLocation')" :problem-type="problemType" :form="form" />

                <RejectedWasteStep v-if="isStep('rejected_waste')" :problem-type="problemType" :form="form" />
                <ContainerStep v-if="isStep('container')" :problem-type="problemType" :form="form" />
                <ScheduleStep v-if="isStep('schedule')" :problem-type="problemType" :form="form" />
                <OtherStep v-if="isStep('other')" :problem-type="problemType" :form="form" />

                <div v-if="form.errors.unchanged" class="text-sm text-red-600" role="alert">
                    <p v-text="form.errors.unchanged" />
                </div>
            </div>
        </template>

        <template #footer="{ close }">
            <Button
                :label="secondaryButtonLabel"
                @click="() => previousStep(close)"
                :disabled="form.validating || form.processing"
                size="sm"
            />

            <Button
                v-if="!isStep('changePinLocation') || form.address_override === null"
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
    import { router, usePage } from '@inertiajs/vue3';
    import { useForm } from 'laravel-precognition-vue-inertia';
    import { trans } from 'laravel-vue-i18n';
    import { ArrowLeftIcon } from '@heroicons/vue/20/solid';
    import route from '@/Helpers/useRoute.js';
    import Button from '@/Components/Button.vue';
    import Modal from '@/Components/Modal.vue';

    import ProblemTypeStep from '@/Components/ReportPointSteps/ProblemType.vue';
    import AddressStep from '@/Components/ReportPointSteps/Address.vue';
    import LocationStep from '@/Components/ReportPointSteps/Location.vue';
    import ChangePinLocationStep from '@/Components/ReportPointSteps/ChangePinLocation.vue';
    import ContainerStep from '@/Components/ReportPointSteps/Container.vue';
    import RejectedWasteStep from '@/Components/ReportPointSteps/RejectedWaste.vue';
    import ScheduleStep from '@/Components/ReportPointSteps/Schedule.vue';
    import OtherStep from '@/Components/ReportPointSteps/Other.vue';

    const page = usePage();

    const originalLocation = ref({
        lat: null,
        lng: null,
    });

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

    const form = useForm('post', route('front.submit.report', { point: props.point }), {
        step: 'type',

        // step 1: Type
        type_id: null,

        // step 1 & 2: Location
        address: props.point.address,
        address_override: null,
        location: {
            lat: props.point.latlng[0],
            lng: props.point.latlng[1],
        },
        city: null,
        county: null,

        description: null,
        images: [],
        sub_types: [],
    });

    const problemType = computed(() => page.props.problem_types.find((type) => type.id === form.type_id));

    const primaryButtonLabel = computed(() => {
        if (isLastStep.value) {
            return trans('report.action.submit');
        }

        if (isStep('location')) {
            return trans('report.action.save');
        }

        return trans('report.action.next_step');
    });

    const secondaryButtonLabel = computed(() => {
        if (isStep('type')) {
            return trans('report.action.cancel');
        }

        if (isStep('changePinLocation')) {
            return trans('report.action.reset');
        }

        return trans('report.action.back');
    });

    const getFieldsByStep = () =>
        ({
            type: ['type_id'],
            address: ['address', 'city', 'county', 'location.lat', 'location.lng'],
            location: ['address', 'city', 'county', 'location.lat', 'location.lng'],
            // materials: ['materials'],
            rejected_waste: ['description', 'sub_types'],
            container: ['description', 'images'],
            schedule: ['description'],
            other: ['description', 'images'],
        })[form.step];

    const changePinLocation = () => {
        originalLocation.value.lat = form.location.lat;
        originalLocation.value.lng = form.location.lng;

        goToStep('changePinLocation');
    };

    const resetLocation = () => {
        form.location.lat = originalLocation.value.lat;
        form.location.lng = originalLocation.value.lng;

        originalLocation.value.lat = null;
        originalLocation.value.lng = null;
    };

    const transform = (data) => {
        data.images = data.images.map((image) => image.uuid);

        return data;
    };

    const submit = () => {
        if (isStep('location')) {
            return nextStep();
        }

        if (!isLastStep.value) {
            return form.transform(transform).touch(getFieldsByStep()).validate({
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

    const isLastStep = computed(() => {
        if (!problemType.value) {
            return false;
        }

        console.log(problemType.value);

        if (!problemType.value.children || !problemType.value.children.length) {
            return problemType.value.slug === form.step;
        }
    });

    const isStep = (step) => form.step === step;

    const goToStep = (step) => (form.step = step);

    const previousStep = (close) => {
        if (isStep('type')) {
            close();
            form.reset();
            form.clearErrors();
            return;
        }

        if (isStep('changePinLocation')) {
            resetLocation();
            return goToStep(problemType.value.slug);
        }

        // if (isStep('details')) {
        //     return goToStep('type');
        // }

        // if (isStep('materials')) {
        //     return goToStep('details');
        // }

        // if (isStep('review')) {
        //     if (!serviceType.value.can.collect_materials) {
        //         return goToStep('details');
        //     }

        //     return goToStep('materials');
        // }

        if (page.props.problem_types.some((type) => type.slug === form.step)) {
            form.reset();
            form.clearErrors();
            return;
        }
    };

    const nextStep = () => {
        if (isStep('type')) {
            if (problemType.value.slug) {
                return goToStep(problemType.value.slug);
            }
        }

        if (isStep('changePinLocation')) {
            return goToStep(problemType.value.slug);
        }

        // if (isStep('details')) {
        //     if (!serviceType.value.can.collect_materials) {
        //         return goToStep('review');
        //     }

        //     return goToStep('materials');
        // }

        // if (isStep('materials')) {
        //     return goToStep('review');
        // }

        // if (isStep('review')) {
        //     return goToStep('thanks');
        // }
    };

    const cancelAddressOverride = (close) => {
        close();

        form.address_override = null;

        goToStep(problemType.value.slug);
    };

    const confirmAddressOverride = (close) => {
        close();
        form.address = form.address_override;
        form.address_override = null;
        console.log(problemType.value);
        goToStep(problemType.value.slug);
    };

    const showConfirmAddressOverrideModal = ref(false);
</script>
