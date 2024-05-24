<template>
    <Disclosure as="header" class="bg-primary-600 border-b border-gray-200 shadow md:bg-primary-600" v-slot="{ open }">
        <div class="flex justify-between px-4 py-2 md:px-6 md:py-4 md:gap-6">
            <Icon icon="logo-white" class="w-24 h-8 md:w-32 text-white md:h-10 shrink-0"/>

            <div class="hidden md:flex md:gap-4 md:items-center">
                <PillButton color="white" :label="$t('top_menu.back')" :icon="ArrowLongLeftIcon" href="/"/>
                <PillButton
                    color="white"
                    :label="name"
                    :icon="UserIcon"
                    external
                />


            </div>
        </div>

    </Disclosure>
</template>

<script setup>
import {computed} from 'vue';
import {usePage} from '@inertiajs/vue3';
import {Disclosure} from '@headlessui/vue';
import {ArrowLongLeftIcon} from '@heroicons/vue/24/outline';

import Icon from '@/Components/Icon.vue';
import PillButton from '@/Components/Buttons/PillButton.vue';
import {UserIcon} from "@heroicons/vue/20/solid";

const name = computed(() => usePage().props.user.firstname + ' ' + usePage().props.user.lastname);

</script>

