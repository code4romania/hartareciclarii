<template>
    <div class="h-full">
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative z-50 lg:hidden" @close="sidebarOpen = false">
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
        <div class="hidden lg:absolute lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <Sidebar class="border-r border-gray-200" />
        </div>

        <div class="relative h-full lg:pl-72">
            <Search class="inset-x-3 top-3 z-[99999] lg:left-80 lg:right-auto" />

            <Map />
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
    import {
        CalendarIcon,
        ChartPieIcon,
        DocumentDuplicateIcon,
        FolderIcon,
        HomeIcon,
        UsersIcon,
        XMarkIcon,
    } from '@heroicons/vue/24/outline';
    import Map from '@/Components/Map/Map.vue';
    import Sidebar from '@/Components/Map/Sidebar.vue';
    import Search from '@/Components/Map/Search.vue';

    const navigation = [
        { name: 'Dashboard', href: '#', icon: HomeIcon, current: true },
        { name: 'Team', href: '#', icon: UsersIcon, current: false },
        { name: 'Projects', href: '#', icon: FolderIcon, current: false },
        { name: 'Calendar', href: '#', icon: CalendarIcon, current: false },
        { name: 'Documents', href: '#', icon: DocumentDuplicateIcon, current: false },
        { name: 'Reports', href: '#', icon: ChartPieIcon, current: false },
    ];
    const teams = [
        { id: 1, name: 'Heroicons', href: '#', initial: 'H', current: false },
        { id: 2, name: 'Tailwind Labs', href: '#', initial: 'T', current: false },
        { id: 3, name: 'Workcation', href: '#', initial: 'W', current: false },
    ];

    const userNavigation = [
        { name: 'Your profile', href: '#' },
        { name: 'Sign out', href: '#' },
    ];

    const sidebarOpen = ref(true);
</script>
