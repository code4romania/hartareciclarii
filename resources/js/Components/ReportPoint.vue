<template>
    <Modal @submit="submit" :open="open" @close="close" form>
        <template #title>
            {{ $t('report.title') }}
        </template>

        <template #default>
            <div class="grid gap-4">
                <ProblemTypeStep v-if="isStep('type')" :form="form" />

                <LocationStep v-if="isStep('location')" :form="form" />
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
                v-if="!isStep('location') || form.address_override === null"
                type="submit"
                :label="primaryButtonLabel"
                :disabled="form.validating || form.processing"
                size="sm"
                primary
            />
        </template>
    </Modal>
</template>

<script setup>
    import { computed } from 'vue';
    import { router, usePage } from '@inertiajs/vue3';
    import { useForm } from 'laravel-precognition-vue-inertia';
    import { trans } from 'laravel-vue-i18n';
    import route from '@/Helpers/useRoute.js';
    import Button from '@/Components/Button.vue';
    import Modal from '@/Components/Modal.vue';
    import ProblemTypeStep from '@/Components/ReportPointSteps/ProblemType.vue';
    import LocationStep from '@/Components/ReportPointSteps/Location.vue';

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

    const form = useForm('post', route('front.submit.report', { point: props.point }), {
        step: 'type',

        // step 1: Type
        problem_type_id: null,

        // step 1 & 2: Location
        address: null,
        address_override: null,
        location: {
            lat: null,
            lng: null,
        },
        city: null,
        county: null,
    });

    const primaryButtonLabel = computed(
        () =>
            ({
                type: trans('report.action.next_step'),
                location: trans('report.action.save'),
                details: trans('report.action.next_step'),
                materials: trans('report.action.next_step'),
                review: trans('report.action.finish_steps'),
            })[form.step]
    );

    const secondaryButtonLabel = computed(
        () =>
            ({
                type: trans('report.action.cancel'),
                location: trans('report.action.reset'),
                details: trans('report.action.back'),
                materials: trans('report.action.back'),
                review: trans('report.action.back'),
            })[form.step]
    );

    const transform = (data) => {
        return data;
    };

    const submit = () => {
        if (isStep('location')) {
            return nextStep();
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

        // if (isStep('location')) {
        //     resetLocation();
        //     return goToStep('type');
        // }

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
    };

    const nextStep = (event) => {
        console.log(event);
        // if (isStep('type')) {
        //     return goToStep('details');
        // }

        // if (isStep('location')) {
        //     return goToStep('type');
        // }

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
</script>
