<template>
    <Modal @submit="submit" :open="open" @close="close" form>
        <template v-if="!form.wasSuccessful" #title>
            <div v-if="isStep('changePinLocation')" class="flex gap-2">
                <button type="button" @click="previousStep">
                    <ArrowLeftIcon class="w-6 h-6 text-gray-400" />
                </button>

                <span v-text="$t('add_point.type.change_pin_location')" />
            </div>

            <span v-else v-text="$t('report.title')" />
        </template>

        <template #default>
            <ThanksStep v-if="form.wasSuccessful" :problem-type="problemType" :point="point" :close="close" />

            <div v-else class="grid gap-4">
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

                <MaterialsTypeStep v-if="isStep('materials')" :problem-type="problemType" :form="form" />
                <MaterialsAddStep
                    v-if="isStep('materials_add')"
                    :problem-type="problemType.children.find((type) => type.slug === 'materials_add')"
                    :preselected-materials="point.material_ids"
                    :form="form"
                />
                <MaterialsRemoveStep
                    v-if="isStep('materials_remove')"
                    :problem-type="problemType.children.find((type) => type.slug === 'materials_remove')"
                    :preselected-materials="point.material_ids"
                    :form="form"
                />
                <MaterialsOtherStep
                    v-if="isStep('materials_other')"
                    :problem-type="problemType.children.find((type) => type.slug === 'materials_other')"
                    :form="form"
                />

                <RejectedStep
                    v-if="isStep('rejected_waste') || isStep('rejected_repair')"
                    :problem-type="problemType"
                    :form="form"
                />
                <ContainerStep v-if="isStep('container')" :problem-type="problemType" :form="form" />
                <ScheduleStep v-if="isStep('schedule')" :problem-type="problemType" :form="form" />
                <OtherStep v-if="isStep('other')" :problem-type="problemType" :form="form" />

                <div v-if="form.errors.unchanged" class="text-sm text-red-600" role="alert">
                    <p v-text="form.errors.unchanged" />
                </div>
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
    import RejectedStep from '@/Components/ReportPointSteps/Rejected.vue';
    import ScheduleStep from '@/Components/ReportPointSteps/Schedule.vue';
    import OtherStep from '@/Components/ReportPointSteps/Other.vue';
    import MaterialsTypeStep from '@/Components/ReportPointSteps/MaterialsType.vue';
    import MaterialsAddStep from '@/Components/ReportPointSteps/MaterialsAdd.vue';
    import MaterialsRemoveStep from '@/Components/ReportPointSteps/MaterialsRemove.vue';
    import MaterialsOtherStep from '@/Components/ReportPointSteps/MaterialsOther.vue';
    import ThanksStep from '@/Components/ReportPointSteps/Thanks.vue';

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
        materials_add: [],
        materials_remove: [],
    });

    const problemType = computed(() => page.props.problem_types.find((type) => type.id === form.type_id));

    const primaryButtonLabel = computed(() => {
        if (isLastStep.value) {
            return trans('action.submit');
        }

        if (isStep('changePinLocation')) {
            return trans('action.save');
        }

        return trans('action.next_step');
    });

    const secondaryButtonLabel = computed(() => {
        if (isStep('type')) {
            return trans('action.cancel');
        }

        if (isStep('changePinLocation')) {
            return trans('action.reset');
        }

        return trans('action.back');
    });

    const getFieldsByStep = () =>
        ({
            type: ['type_id'],
            address: ['address', 'city', 'county', 'location.lat', 'location.lng'],
            location: ['address', 'city', 'county', 'location.lat', 'location.lng'],
            materials: ['sub_types'],
            materials_remove: ['materials_remove'],
            materials_add: ['materials_add'],
            materials_other: ['description'],
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
        if (isStep('changePinLocation')) {
            return nextStep();
        }

        if (!isLastStep.value) {
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

    const sortedMaterialTypes = computed(() => form.sub_types.sort());

    const getMaterialsTypeStep = (id) => {
        if (problemType.value.slug !== 'materials' || typeof id === 'undefined') {
            return null;
        }

        return problemType.value.children.find((type) => type.id === id)?.slug;
    };

    const getMaterialsTypeId = (slug) => {
        if (problemType.value.slug !== 'materials' || !slug) {
            return null;
        }

        return problemType.value.children.find((type) => type.slug === slug)?.id;
    };

    const isLastStep = computed(() => {
        if (!problemType.value) {
            return false;
        }

        if (problemType.value.slug === 'materials') {
            let [lastMaterialStep] = sortedMaterialTypes.value.slice(-1);

            return form.step === getMaterialsTypeStep(lastMaterialStep);
        }

        return problemType.value.slug === form.step;
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

        if (problemType.value.slug === 'materials') {
            if (form.step === 'materials') {
                return goToStep('type');
            }

            const currentIndex = sortedMaterialTypes.value.indexOf(getMaterialsTypeId(form.step));

            if (currentIndex > 0) {
                return goToStep(getMaterialsTypeStep(sortedMaterialTypes.value[currentIndex - 1]));
            }

            return goToStep(problemType.value.slug);
        }

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

        if (problemType.value.slug === 'materials') {
            if (form.step === 'materials') {
                return goToStep(getMaterialsTypeStep(sortedMaterialTypes.value[0]));
            }

            const currentIndex = sortedMaterialTypes.value.indexOf(getMaterialsTypeId(form.step));

            if (currentIndex < sortedMaterialTypes.value.length) {
                return goToStep(getMaterialsTypeStep(sortedMaterialTypes.value[currentIndex + 1]));
            }
        }
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
        goToStep(problemType.value.slug);
    };

    const showConfirmAddressOverrideModal = ref(false);
</script>
