<template>
    <Disclosure as="header" class="relative bg-white border-b border-gray-200 shadow lg:bg-gray-50" v-slot="{ open }">
        <div class="flex justify-between px-4 py-3 md:px-6 md:gap-6">
            <Link :href="route('front.map.index')">
                <Icon icon="logo" class="w-32 h-11 shrink-0" />
            </Link>

            <AddPoint
                v-if="!dashboard"
                :open="isAddPointOpen"
                @open="isAddPointOpen = true"
                @close="isAddPointOpen = false"
            />

            <div class="hidden lg:flex lg:gap-4 lg:items-center">
                <Button
                    v-if="!dashboard"
                    :label="$t('top_menu.add_point')"
                    :icon="MapPinIcon"
                    @click="openAddPointModal"
                />

                <Button
                    v-for="(item, index) in navigation"
                    :key="index"
                    :label="item.label"
                    :icon="item.icon"
                    :href="item.href"
                    :external="item.external"
                />

                <!-- Profile dropdown -->
                <Menu v-if="$page.props.auth" as="div" v-slot="{ close }" class="relative">
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
                            <MenuItem
                                v-for="item in userNavigation"
                                :key="item.name"
                                :as="Button"
                                size="sm"
                                :href="item.href"
                                class="block px-4 py-2 text-sm text-gray-700"
                                @click="close"
                                :method="item?.method"
                                :label="item.label"
                                simple
                            />
                        </MenuItems>
                    </transition>
                </Menu>

                <Button v-else :label="$t('auth.login')" :icon="UserIcon" :href="route('auth.login')" />
            </div>

            <div class="flex items-center -mr-2 lg:hidden">
                <!-- Mobile menu button -->
                <DisclosureButton
                    class="relative inline-flex items-center justify-center p-2 text-gray-400 bg-white rounded-md hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    <span class="absolute -inset-0.5" />
                    <span class="sr-only">Open main menu</span>
                    <Bars3Icon v-if="!open" class="block w-6 h-6" aria-hidden="true" />
                    <XMarkIcon v-else class="block w-6 h-6" aria-hidden="true" />
                </DisclosureButton>
            </div>
        </div>

        <DisclosurePanel
            class="absolute inset-x-0 z-20 bg-white border-b border-gray-200 shadow lg:hidden top-full"
            v-slot="{ close }"
        >
            <div class="px-2 pt-2 pb-3 sm:px-3" v-click-away="close">
                <div class="flex flex-col gap-1 pt-2 pb-3">
                    <DisclosureButton
                        v-if="!dashboard"
                        :label="$t('top_menu.add_point')"
                        :icon="MapPinIcon"
                        @click="openAddPointModal"
                        :as="Button"
                        class="w-full"
                        simple
                    />

                    <DisclosureButton
                        v-for="(item, index) in navigation"
                        :key="index"
                        :icon="item.icon"
                        :label="item.label"
                        :href="item.href"
                        :external="item.external"
                        @click="() => close() && item?.click()"
                        :as="Button"
                        simple
                    />
                </div>

                <div v-if="$page.props.auth" class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <img class="w-10 h-10 rounded-full" :src="$page.props.auth.avatar" alt="" />
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ $page.props.auth.full_name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.email }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col mt-3">
                        <DisclosureButton
                            v-for="(item, index) in userNavigation"
                            :key="index"
                            :href="item.href"
                            :label="item.label"
                            :method="item.method"
                            class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800"
                            @click="close"
                            :as="Button"
                            simple
                        />
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
    import { Link } from '@inertiajs/vue3';
    import { trans } from 'laravel-vue-i18n';
    import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
    import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
    import {
        Bars3Icon,
        XMarkIcon,
        BookOpenIcon,
        QuestionMarkCircleIcon,
        UserIcon,
        MapPinIcon,
        MapIcon,
    } from '@heroicons/vue/20/solid';

    import Icon from '@/Components/Icon.vue';
    import Button from '@/Components/Button.vue';
    import AddPoint from '@/Components/Navigation/AddPoint.vue';
    import route from '@/Helpers/useRoute.js';

    const props = defineProps({
        dashboard: {
            type: Boolean,
            default: false,
        },
    });

    const isAddPointOpen = ref(false);

    const openAddPointModal = (close) => {
        isAddPointOpen.value = true;
    };

    const navigation = computed(() => {
        if (props.dashboard) {
            return [
                {
                    icon: MapIcon,
                    label: trans('top_menu.back'),
                    href: route('front.map.index'),
                },
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
        }

        return [
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
    });

    const userNavigation = [
        { label: trans('profile.my_profile'), href: route('front.account.dashboard') },
        { label: trans('profile.settings'), href: route('front.account.settings') },
        { label: trans('profile.logout'), href: route('auth.logout'), method: 'post' },
    ];
</script>
