<template>
    <div>
        <PillButton color="white" :label="$t('top_menu.add_point')" :icon="MapPinIcon" @click="open" />

        <TransitionRoot appear :show="isModalOpen" as="template">
            <Dialog as="div" @close="close" class="relative z-10">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-full p-4 text-center">
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-3xl"
                            >
                                <DialogTitle
                                    as="div"
                                    class="flex items-center justify-between text-lg font-medium leading-6 text-gray-900"
                                >
                                    <h3 v-text="$t('add_point.title')" />
                                    <button type="button" class="text-gray-400 hover:text-gray-500" @click="close">
                                        <XMarkIcon class="w-6 h-6" aria-hidden="true" />
                                    </button>
                                </DialogTitle>

                                <form @submit.prevent="submitForm" class="mt-2">
                                    <div v-show="currentStep === 1">
                                        <div class="mt-6">
                                            <p v-text="$t('add_point.first_step.subtitle')" />
                                        </div>
                                        <div class="grid gap-4 mt-8">
                                            <SearchList
                                                :label="$t('add_point.first_step.service_type_label')"
                                                v-model="form.service_type"
                                                :options="serviceTypes"
                                            />
                                            <small
                                                class="text-red-600"
                                                v-if="form.errors.service_type"
                                                v-text="form.errors.service_type"
                                            />
                                            <SearchWithMap v-model="form.address" @set-point="setPoint" />
                                            <small
                                                class="text-red-600"
                                                v-if="form.errors.address"
                                                v-text="form.errors.address"
                                            />
                                        </div>
                                    </div>
                                    <div v-show="currentStep === 2">
                                        <div class="mt-6">
                                            <p v-text="$t('add_point.second_step.subtitle')" />
                                        </div>

                                        <div class="grid gap-4 mt-8">
                                            <SearchList
                                                :label="$t('add_point.second_step.collected_materials')"
                                                v-model="form.point_type"
                                                :options="pointTypes"
                                            />
                                            <small
                                                class="text-red-600"
                                                v-if="form.errors.service_type"
                                                v-text="form.errors.point_type"
                                            />

                                            <div class="flex flex-wrap justify-end">
                                                <div class="w-full">
                                                    <label
                                                        for="administrate_by"
                                                        class="block text-sm font-semibold text-gray-900"
                                                        v-text="$t('add_point.second_step.administration')"
                                                    />
                                                    <input
                                                        type="text"
                                                        v-model="form.administrated_by"
                                                        :disabled="unknownAdministration"
                                                        id="administrate_by"
                                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500 disabled:ring-gray-200 sm:text-sm sm:leading-6"
                                                        :class="{ 'bg-gray-500': unknownAdministration }"
                                                        :placeholder="
                                                            $t('add_point.second_step.administration_placeholder')
                                                        "
                                                    />
                                                </div>

                                                <div class="flex mt-3">
                                                    <div class="flex items-center h-6">
                                                        <input
                                                            id="unknown_administration"
                                                            aria-describedby="comments-description"
                                                            name="unknown_administration"
                                                            type="checkbox"
                                                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-600"
                                                            v-model="unknownAdministration"
                                                        />
                                                    </div>
                                                    <div class="ml-3 text-sm leading-6">
                                                        <label
                                                            for="unknown_administration"
                                                            class="font-medium text-gray-900"
                                                            v-text="$t('add_point.second_step.unknown_administration')"
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap justify-end">
                                                <div class="w-full">
                                                    <label
                                                        for="administrate_by"
                                                        class="block text-sm font-semibold text-gray-900"
                                                        v-text="$t('add_point.second_step.administration')"
                                                    />
                                                    <input
                                                        type="text"
                                                        v-model="form.administrated_by"
                                                        :disabled="unknownAdministration"
                                                        id="administrate_by"
                                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500 disabled:ring-gray-200 sm:text-sm sm:leading-6"
                                                        :class="{ 'bg-gray-500': unknownAdministration }"
                                                        :placeholder="
                                                            $t('add_point.second_step.administration_placeholder')
                                                        "
                                                    />
                                                </div>

                                                <div class="flex mt-3">
                                                    <div class="flex items-center h-6">
                                                        <input
                                                            id="unknown_administration"
                                                            aria-describedby="comments-description"
                                                            name="unknown_administration"
                                                            type="checkbox"
                                                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-600"
                                                            v-model="unknownAdministration"
                                                        />
                                                    </div>
                                                    <div class="ml-3 text-sm leading-6">
                                                        <label
                                                            for="unknown_administration"
                                                            class="font-medium text-gray-900"
                                                            v-text="$t('add_point.second_step.unknown_administration')"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex mt-4 space-x-4 place-content-end">
                                        <button
                                            type="reset"
                                            class="inline-flex justify-center px-3 py-2 text-sm bg-white border border-black rounded-md shadow-sm text-primary-500 sm:col-end-2"
                                            @click="close"
                                            v-text="$t('add_point.cancel')"
                                        />
                                        <button
                                            type="button"
                                            v-if="currentStep > 1"
                                            @click="currentStep = currentStep - 1"
                                            class="inline-flex justify-center px-3 py-2 text-sm text-white border border-black rounded-md shadow-sm bg-primary-500 sm:col-end-2"
                                            v-text="$t('add_point.back')"
                                        />

                                        <button
                                            type="submit"
                                            class="inline-flex justify-center px-3 py-2 text-sm text-white border border-black rounded-md shadow-sm bg-primary-500 sm:col-end-2"
                                            v-text="$t('add_point.next_step')"
                                        />
                                    </div>
                                </form>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { useForm, usePage } from '@inertiajs/vue3';
    import { trans } from 'laravel-vue-i18n';
    import { ComboboxInput, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
    import { MapPinIcon } from '@heroicons/vue/20/solid/index.js';
    import { XMarkIcon } from '@heroicons/vue/20/solid';

    import PillButton from '@/Components/Buttons/PillButton.vue';
    import SearchList from '@/Components/AddPoint/SearchList.vue';
    import SearchWithMap from '@/Components/AddPoint/SearchWithMap.vue';

    const page = usePage();
    const currentStep = ref(1);
    const isModalOpen = ref(false);
    const errors = computed(() => page.props.errors || {});

    const open = () => {
        isModalOpen.value = true;
    };

    const close = () => {
        isModalOpen.value = false;
    };

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
        e.preventDefault();

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
