<template>
    <Disclosure as="header" class="bg-white border-b border-gray-200 shadow md:bg-gray-50" v-slot="{ open }">
        <div class="flex justify-between px-4 py-2 md:px-6 md:py-4 md:gap-6">
            <Icon icon="logo" class="w-24 h-8 md:w-32 md:h-10 shrink-0" />

            <div class="hidden md:flex md:gap-4 md:items-center">
                <AddPoint />

                <PillButton
                    color="white"
                    :label="$t('top_menu.dictionary')"
                    :icon="BookOpenIcon"
                    href="https://hartareciclarii.ro/ce-si-cum-reciclez/#/category/all"
                    external
                />

                <PillButton
                    color="white"
                    :label="$t('top_menu.faq')"
                    :icon="QuestionMarkCircleIcon"
                    href="https://hartareciclarii.ro/despre-proiect/intrebari-frecvente/"
                    external
                />

                <!-- Profile dropdown -->
                <Menu v-if="user" as="div" class="relative ml-3">
                    <MenuButton :as="PillButton" color="white" :icon="UserIcon" :label="user.full_name" />

                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="transform scale-95 opacity-0"
                        enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0"
                    >
                        <MenuItems
                            class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                            <MenuItem v-for="item in userNavigation" :key="item.name" v-slot="{ active }">
                                <a
                                    :href="item.href"
                                    :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']"
                                    >{{ item.name }}</a
                                >
                            </MenuItem>
                        </MenuItems>
                    </transition>
                </Menu>

                <Login v-else />
            </div>

            <div class="flex items-center -mr-2 md:hidden">
                <!-- Mobile menu button -->
                <DisclosureButton
                    class="relative inline-flex items-center justify-center p-1 text-gray-400 bg-white rounded-md hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    <span class="absolute -inset-0.5" />
                    <span class="sr-only">Open main menu</span>
                    <Bars3Icon v-if="!open" class="block w-6 h-6" aria-hidden="true" />
                    <XMarkIcon v-else class="block w-6 h-6" aria-hidden="true" />
                </DisclosureButton>
            </div>
        </div>

        <DisclosurePanel class="md:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <DisclosureButton
                    v-for="item in navigation"
                    :key="item.name"
                    as="a"
                    :href="item.href"
                    :class="[
                        item.current
                            ? 'border-primary-500 bg-primary-50 text-primary-700'
                            : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800',
                        'block border-l-4 py-2 pl-3 pr-4 text-base font-medium',
                    ]"
                    :aria-current="item.current ? 'page' : undefined"
                >
                    {{ item.name }}
                </DisclosureButton>
            </div>
            <div v-if="user" class="pt-4 pb-3 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <div class="flex-shrink-0">
                        <img class="w-10 h-10 rounded-full" :src="user.imageUrl" alt="" />
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium text-gray-800">{{ user.name }}</div>
                        <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
                    </div>
                    <button
                        type="button"
                        class="relative flex-shrink-0 p-1 ml-auto text-gray-400 bg-white rounded-full hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        <span class="absolute -inset-1.5" />
                        <span class="sr-only">View notifications</span>
                        <BellIcon class="w-6 h-6" aria-hidden="true" />
                    </button>
                </div>
                <div class="mt-3 space-y-1">
                    <DisclosureButton
                        v-for="item in userNavigation"
                        :key="item.name"
                        as="a"
                        :href="item.href"
                        class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800"
                        >{{ item.name }}</DisclosureButton
                    >
                </div>
            </div>
        </DisclosurePanel>
    </Disclosure>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
    import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import { BookOpenIcon, QuestionMarkCircleIcon, UserIcon } from '@heroicons/vue/20/solid';

    import Icon from '@/Components/Icon.vue';
    import PillButton from '@/Components/Buttons/PillButton.vue';
    import Login from '@/Components/Navigation/Login.vue';
    import AddPoint from '@/Components/Navigation/AddPoint.vue';
    import { trans } from 'laravel-vue-i18n';
    import route from '@/Helpers/useRoute.js';

    const navigation = [
        { name: 'Dashboard', href: '#', current: true },
        { name: 'Team', href: '#', current: false },
        { name: 'Projects', href: '#', current: false },
        { name: 'Calendar', href: '#', current: false },
    ];

    const userNavigation = [
        { name: trans('profile.my_profile'), href: route('dashboard') },
        { name: trans('profile.settings'), href: route('profile.edit') },
        { name: trans('profile.logout'), href: route('logout') },
    ];

    const showLoginModal = ref(true);

    const user = computed(() => usePage().props.user);
</script>

