<template>
    <Disclosure
        as="header"
        class="relative z-20 bg-white border-b border-gray-200 shadow md:bg-gray-50"
        v-slot="{ open }"
    >
        <div class="flex justify-between px-4 py-2 md:px-6 md:py-4 md:gap-6">
            <Link :href="route('front.map.index')">
                <Icon icon="logo" class="w-24 h-8 md:w-32 md:h-10 shrink-0" />
            </Link>

            <AddPoint :open="isAddPointOpen" @open="isAddPointOpen = true" @close="isAddPointOpen = false" />

            <div class="hidden lg:flex lg:gap-4 lg:items-center">
                <Button :label="$t('top_menu.add_point')" :icon="MapPinIcon" @click="openAddPointModal" />
                <Button
                    v-for="(item, index) in navigation"
                    :key="index"
                    :label="item.label"
                    :icon="item.icon"
                    :href="item.href"
                    :external="item.external"
                />

                <!-- Profile dropdown -->
                <Menu v-if="$page.props.auth" as="div" class="relative">
                    <MenuButton :as="Button" color="white" :icon="UserIcon" :label="$page.props.auth.full_name" />

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
                                <Link
                                    :href="item.href"
                                    :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']"
                                    :method="item.method || 'get'"
                                >
                                    {{ item.label }}
                                </Link>
                            </MenuItem>
                        </MenuItems>
                    </transition>
                </Menu>

                <Button v-else :label="$t('auth.login')" :icon="UserIcon" :href="route('auth.login')" />
            </div>

            <div class="flex items-center -mr-2 lg:hidden">
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

        <DisclosurePanel
            class="absolute inset-x-0 bg-white border-b border-gray-200 shadow lg:hidden top-full"
            v-slot="{ close }"
        >
            <div class="px-2 pt-2 pb-3 sm:px-3" v-click-away="close">
                <div class="pt-2 pb-3 space-y-1">
                    <DisclosureButton
                        :label="$t('top_menu.add_point')"
                        :icon="MapPinIcon"
                        @click="openAddPointModal"
                        :as="Button"
                        simple
                    />

                    <DisclosureButton
                        v-for="(item, index) in navigation"
                        :key="index"
                        :icon="item.icon"
                        :href="item.href"
                        :external="item.external"
                        @click="() => item.hasOwnProperty('click') && item.click()"
                        :as="Button"
                        simple
                    >
                        {{ item.label }}
                    </DisclosureButton>
                </div>

                <div v-if="$page.props.auth" class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <img class="w-10 h-10 rounded-full" :src="$page.props.auth.imageUrl" alt="" />
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ $page.props.auth.full_name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <DisclosureButton
                            v-for="(item, index) in userNavigation"
                            :key="index"
                            :as="Link"
                            :href="item.href"
                            class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800"
                            :method="item.method || 'get'"
                        >
                            {{ item.label }}
                        </DisclosureButton>
                    </div>
                </div>

                <DisclosureButton
                    v-else
                    :label="$t('auth.login')"
                    :href="route('auth.login')"
                    :icon="UserIcon"
                    :as="Button"
                    simple
                />
            </div>
        </DisclosurePanel>
    </Disclosure>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { usePage, Link } from '@inertiajs/vue3';
    import {
        Disclosure,
        DisclosureButton,
        DisclosurePanel,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
    } from '@headlessui/vue';
    import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import { BookOpenIcon, QuestionMarkCircleIcon, UserIcon, MapPinIcon } from '@heroicons/vue/20/solid';

    import Icon from '@/Components/Icon.vue';
    import Button from '@/Components/Button.vue';
    import AddPoint from '@/Components/Navigation/AddPoint.vue';
    import { trans } from 'laravel-vue-i18n';
    import route from '@/Helpers/useRoute.js';

    const isAddPointOpen = ref(false);

    const openAddPointModal = () => (isAddPointOpen.value = true);

    const navigation = [
        {
            icon: BookOpenIcon,
            label: trans('top_menu.dictionary'),
            href: 'https://hartareciclarii.ro/ce-si-cum-reciclez/#/category/all',
            external: true,
        },
        {
            icon: QuestionMarkCircleIcon,
            label: trans('top_menu.faq'),
            href: 'https://hartareciclarii.ro/despre-proiect/intrebari-frecvente/',
            external: true,
        },
    ];

    const userNavigation = [
        { label: trans('profile.my_profile'), href: route('front.account.dashboard') },
        { label: trans('profile.settings'), href: route('front.account.settings') },
        { label: trans('profile.logout'), href: route('auth.logout'), method: 'post' },
    ];

    const showLoginModal = ref(true);

    const user = computed(() => usePage().props.user);
</script>
