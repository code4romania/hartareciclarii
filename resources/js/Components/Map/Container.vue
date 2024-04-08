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
                            <div class="flex flex-col px-6 pb-4 overflow-y-auto bg-white grow gap-y-5">
                                <div class="flex items-center h-16 shrink-0">
                                    <img
                                        class="w-auto h-8"
                                        src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600"
                                        alt="Your Company"
                                    />
                                </div>
                                <nav class="flex flex-col flex-1">
                                    <ul role="list" class="flex flex-col flex-1 gap-y-7">
                                        <li>
                                            <ul role="list" class="-mx-2 space-y-1">
                                                <li v-for="item in navigation" :key="item.name">
                                                    <a
                                                        :href="item.href"
                                                        :class="[
                                                            item.current
                                                                ? 'bg-gray-50 text-indigo-600'
                                                                : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50',
                                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                                        ]"
                                                    >
                                                        <component
                                                            :is="item.icon"
                                                            :class="[
                                                                item.current
                                                                    ? 'text-indigo-600'
                                                                    : 'text-gray-400 group-hover:text-indigo-600',
                                                                'h-6 w-6 shrink-0',
                                                            ]"
                                                            aria-hidden="true"
                                                        />
                                                        {{ item.name }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <div class="text-xs font-semibold leading-6 text-gray-400">Your teams</div>
                                            <ul role="list" class="mt-2 -mx-2 space-y-1">
                                                <li v-for="team in teams" :key="team.name">
                                                    <a
                                                        :href="team.href"
                                                        :class="[
                                                            team.current
                                                                ? 'bg-gray-50 text-indigo-600'
                                                                : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50',
                                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                                        ]"
                                                    >
                                                        <span
                                                            :class="[
                                                                team.current
                                                                    ? 'text-indigo-600 border-indigo-600'
                                                                    : 'text-gray-400 border-gray-200 group-hover:border-indigo-600 group-hover:text-indigo-600',
                                                                'flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border text-[0.625rem] font-medium bg-white',
                                                            ]"
                                                            >{{ team.initial }}</span
                                                        >
                                                        <span class="truncate">{{ team.name }}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:absolute lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div class="flex flex-col px-6 pb-4 overflow-y-auto bg-white border-r border-gray-200 grow gap-y-5">
                <div class="flex items-center h-16 shrink-0">
                    <img
                        class="w-auto h-8"
                        src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600"
                        alt="Your Company"
                    />
                </div>
                <nav class="flex flex-col flex-1">
                    <ul role="list" class="flex flex-col flex-1 gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li v-for="item in navigation" :key="item.name">
                                    <a
                                        :href="item.href"
                                        :class="[
                                            item.current
                                                ? 'bg-gray-50 text-indigo-600'
                                                : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            :class="[
                                                item.current
                                                    ? 'text-indigo-600'
                                                    : 'text-gray-400 group-hover:text-indigo-600',
                                                'h-6 w-6 shrink-0',
                                            ]"
                                            aria-hidden="true"
                                        />
                                        {{ item.name }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <div class="text-xs font-semibold leading-6 text-gray-400">Your teams</div>
                            <ul role="list" class="mt-2 -mx-2 space-y-1">
                                <li v-for="team in teams" :key="team.name">
                                    <a
                                        :href="team.href"
                                        :class="[
                                            team.current
                                                ? 'bg-gray-50 text-indigo-600'
                                                : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                team.current
                                                    ? 'text-indigo-600 border-indigo-600'
                                                    : 'text-gray-400 border-gray-200 group-hover:border-indigo-600 group-hover:text-indigo-600',
                                                'flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border text-[0.625rem] font-medium bg-white',
                                            ]"
                                            >{{ team.initial }}</span
                                        >
                                        <span class="truncate">{{ team.name }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="h-full lg:pl-72">
            <!-- <div
                class="sticky top-0 z-40 flex items-center h-16 px-4 bg-white border-b border-gray-200 shadow-sm shrink-0 gap-x-4 sm:gap-x-6 sm:px-6 lg:px-8"
            >
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <Bars3Icon class="w-6 h-6" aria-hidden="true" />
                </button>

                <!-- Separator -/->
                <div class="w-px h-6 bg-gray-200 lg:hidden" aria-hidden="true" />

                <div class="flex self-stretch flex-1 gap-x-4 lg:gap-x-6">
                    <form class="relative flex flex-1" action="#" method="GET">
                        <label for="search-field" class="sr-only">Search</label>
                        <MagnifyingGlassIcon
                            class="absolute inset-y-0 left-0 w-5 h-full text-gray-400 pointer-events-none"
                            aria-hidden="true"
                        />
                        <input
                            id="search-field"
                            class="block w-full h-full py-0 pl-8 pr-0 text-gray-900 border-0 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                            placeholder="Search..."
                            type="search"
                            name="search"
                        />
                    </form>
                </div>
            </div> -->

            <Map class="h-full" />
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import {
        Dialog,
        DialogPanel,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        TransitionChild,
        TransitionRoot,
    } from '@headlessui/vue';
    import {
        Bars3Icon,
        BellIcon,
        CalendarIcon,
        ChartPieIcon,
        Cog6ToothIcon,
        DocumentDuplicateIcon,
        FolderIcon,
        HomeIcon,
        UsersIcon,
        XMarkIcon,
    } from '@heroicons/vue/24/outline';
    import { ChevronDownIcon, MagnifyingGlassIcon } from '@heroicons/vue/20/solid';
    import Map from '@/Components/Map/Map.vue';

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

    const sidebarOpen = ref(false);
</script>
