<template>
    <TransitionRoot as="template" :show="sidebarOpen">
        <Dialog as="div" class="relative lg:hidden" @close="sidebarOpen = false">
            <TransitionChild
                as="template"
                enter="transition-opacity ease-linear duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="transition-opacity ease-linear duration-300"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-gray-900/80" />
            </TransitionChild>

            <div class="fixed inset-0 flex">
                <TransitionChild
                    as="template"
                    enter="transition ease-in-out duration-300 transform"
                    enter-from="-translate-x-full"
                    enter-to="translate-x-0"
                    leave="transition ease-in-out duration-300 transform"
                    leave-from="translate-x-0"
                    leave-to="-translate-x-full"
                >
                    <DialogPanel class="relative flex flex-1 w-full max-w-xs mr-16">
                        <TransitionChild
                            as="template"
                            enter="ease-in-out duration-300"
                            enter-from="opacity-0"
                            enter-to="opacity-100"
                            leave="ease-in-out duration-300"
                            leave-from="opacity-100"
                            leave-to="opacity-0"
                        >
                            <div class="absolute top-0 flex justify-center w-16 pt-5 left-full">
                                <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                                    <span class="sr-only">Close sidebar</span>
                                    <XMarkIcon class="w-6 h-6 text-white" aria-hidden="true" />
                                </button>
                            </div>
                        </TransitionChild>
                        <!-- Sidebar component, swap this element with another sidebar if you like -->
                        <Sidebar />
                    </DialogPanel>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:absolute lg:inset-y-0 lg:flex lg:w-72 lg:flex-col">
        <Sidebar class="border-r border-gray-200" />
    </div>

    <div class="relative h-full lg:pl-72">
        <div
            class="absolute z-10 flex flex-col gap-4 overflow-hidden pointer-events-none inset-3 lg:left-80 lg:right-auto sm:w-80 md:w-96"
        >
            <Search class="relative pointer-events-auto" @go-to-point="setSelectedPoint" />

            <slot />
        </div>

        <Map :selected-point="selectedPoint" />
    </div>
</template>

<script setup>
    import DefaultLayout from '@/Layouts/DefaultLayout.vue';
    import { ref } from 'vue';
    import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
    import { XMarkIcon } from '@heroicons/vue/24/outline';
    import Map from '@/Components/Map/Map.vue';
    import Sidebar from '@/Components/Map/Sidebar.vue';
    import Search from '@/Components/Map/Search.vue';

    const selectedPoint = ref(null);

    const props = defineProps({
        selectedPoint: {
            type: Object,
            default: null,
        },
    });

    function setSelectedPoint(e) {
        selectedPoint.value = e;
    }

    const sidebarOpen = ref(true);
</script>
