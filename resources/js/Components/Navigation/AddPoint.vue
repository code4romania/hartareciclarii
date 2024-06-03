<template>
    <div>
        <PillButton color="white" :label="$t('top_menu.add_point')" :icon="MapPinIcon" @click="open" />
        <div>
            <TransitionRoot appear :show="addMapPoint" as="template">
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
                                                <SearchWithMap v-model="form.address" />
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
    </div>
</template>

<script setup>
    import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
    import { XMarkIcon } from '@heroicons/vue/20/solid';
    import PillButton from '@/Components/Buttons/PillButton.vue';
    import { computed, ref } from 'vue';
    import { useForm, usePage } from '@inertiajs/vue3';
    import { MapPinIcon } from '@heroicons/vue/20/solid/index.js';
    import SearchList from '@/Components/AddPoint/SearchList.vue';
    import SearchWithMap from '@/Components/AddPoint/SearchWithMap.vue';

    const addMapPoint = ref(false);

    const open = () => {
        addMapPoint.value = true;
    };

    const close = () => {
        addMapPoint.value = false;
    };

    const form = useForm({
        address: '',
        service_type: '',
        lat: '',
        lng: '',
        errors: {},
    });

    let currentStep = ref(1);

    const serviceTypes = computed(() =>
        Object.entries(usePage().props.service_types).map(([key, value]) => ({
            label: value,
            value: key,
        }))
    );

    const submitForm = (e) => {
        console.log(form, e);
    };

    const errors = computed(() => usePage().props.errors);
</script>
