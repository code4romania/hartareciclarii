<template>
    <div>
        <PillButton color="white" :label="$t('auth.login')" :icon="UserIcon" @click="open" />

        <TransitionRoot appear :show="isOpen" as="template">
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
                    <div
                        class="flex min-h-full items-center justify-center p-4 text-center"
                    >
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
                                class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="div"
                                    class="text-lg font-medium leading-6 text-gray-900 flex items-center justify-between"
                                >
                                    <h3 v-text="$t('auth.login')"/>
                                    <button
                                        type="button"
                                        class="text-gray-400 hover:text-gray-500"
                                        @click="close">
                                        <XMarkIcon class="w-6 h-6" aria-hidden="true" />
                                    </button>

                                </DialogTitle>
                                <div class="mt-2">
                                    <form @submit="submitLogin">
                                        <div class="space-y-1 mb-3">
                                            <label
                                                for="email"
                                                class="block text-sm font-medium leading-6 text-gray-900"
                                                v-text="$t('auth.email')"
                                            />
                                            <div class="mt-2">
                                                <input
                                                    id="email"
                                                    v-model="form.email"
                                                    :placeholder="$t('auth.email')"
                                                    class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                    name="email"
                                                    required
                                                    type="email"
                                                />
                                            </div>
                                        </div>
                                        <div class="space-y-1 mb-1">
                                            <label
                                                for="email"
                                                class="block text-sm font-medium leading-6 text-gray-900"
                                                v-text="$t('auth.password')"
                                            />
                                            <div class="mt-2">
                                                <input
                                                    id="password"
                                                    v-model="form.password"
                                                    :placeholder="$t('auth.password')"
                                                    class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                    name="password"
                                                    required
                                                    type="password"
                                                />
                                            </div>
                                        </div>
                                        <div class="mb-1 text-end">
                                            <a class="cursor-pointer" >{{ $t('auth.recover') }}</a>
                                        </div>
                                        <template v-show="form.errors">
                                            <div class="rounded-md bg-red-50 p-4">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
                                                    </div>
                                                    <div class="ml-3">
                                                        <h3 class="text-sm font-medium text-red-800">{{ $t('auth.error') }}</h3>

                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </form>
                                </div>

                                <div class="mt-4">
                                    <button
                                        type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-black bg-primary-500 px-3 py-2 text-sm text-white shadow-sm sm:col-start-2"
                                        @click="submitLogin"
                                    v-text="$t('auth.login')"/>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
    import { Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild } from '@headlessui/vue';
    import { UserIcon, XMarkIcon } from '@heroicons/vue/20/solid';
    import PillButton from '@/Components/Buttons/PillButton.vue';
    import route from '@/Helpers/useRoute.js';

    import { ref } from 'vue';
    import {useForm} from "@inertiajs/vue3";

    const isOpen = ref(false);

    const open = () => {
        isOpen.value = true;
    };

    const form = useForm({
        'email': '',
        'password': '',
    })

    const submitLogin = () => {
        form.post(route('login'));
    }

    const close = () => {
        isOpen.value = false;
    };
</script>
