<template>
    <div>
        <PillButton color="white" :label="$t('auth.login')" :icon="UserIcon" @click="open" />
        <div>
            <TransitionRoot appear :show="loginModal" as="template">
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
                                    class="w-96 max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left align-middle shadow-xl transition-all"
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
                                        <form @submit="submitLogin" >
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
                                                    for="password"
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
                                            <div class="mt-3 text-end">
                                                <a class="cursor-pointer" >{{ $t('auth.recover') }}</a>
                                            </div>
                                            <template v-if="errors.email">
                                                <div class="rounded-md bg-red-50 p-4">
                                                    <div class="flex">
                                                        <XMarkIcon class="h-5 w-5 text-red-700" aria-hidden="true" />
                                                        <div class="ml-3">
                                                            <h4 class="text-sm font-medium text-red-800">{{errors.email}}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </form>
                                    </div>

                                    <div class="mt-8">
                                        <button
                                            type="submit"
                                            class="w-full inline-flex justify-center rounded-md border border-black bg-primary-500 px-3 py-2 text-sm text-white shadow-sm sm:col-start-2"
                                            @click="submitLogin"
                                            v-text="$t('auth.login')"/>
                                    </div>
                                    <div class="mt-4 flex justify-center">
                                        <p class="text-sm " v-html="$t('auth.register')"/>
                                        <button class="text-sm btn text-primary-500 ml-2" v-text="$t('auth.register_link')" @click="registerOpen"/>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>
        </div>

        <div>
            <TransitionRoot appear :show="registerModal" as="template">
                <Dialog as="div" @close="registerClose" class="relative z-10">
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
                                    class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left align-middle shadow-xl transition-all h-1/2"
                                >
                                    <DialogTitle
                                        as="div"
                                        class="text-lg font-medium leading-6 text-gray-900 flex items-center justify-between"
                                    >
                                        <h3 v-text="$t('auth.register')"/>
                                        <button
                                            type="button"
                                            class="text-gray-400 hover:text-gray-500"
                                            @click="registerClose">
                                            <XMarkIcon class="w-6 h-6" aria-hidden="true" />
                                        </button>

                                    </DialogTitle>
                                    <div class="mt-2">
                                        <form @submit="submitRegister" class="mt-8">
                                            <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                                <InputComponent
                                                    id="first-name"
                                                    :label="$t('auth.register.firstname')"
                                                    v-model="registerForm.first_name"
                                                    :error="errors.first_name"
                                                    is-required
                                                />
                                                <InputComponent
                                                    id="last-name"
                                                    :label="$t('auth.register.lastname')"
                                                    v-model="registerForm.last_name"
                                                    :error="errors.last_name"
                                                    is-required
                                                />
                                                <div class="sm:col-span-2">
                                                    <InputComponent
                                                        id="phone"
                                                        :label="$t('auth.register.phone')"
                                                        v-model="registerForm.phone"
                                                        :error="errors.phone"
                                                        type="tel"
                                                    />
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <InputComponent
                                                        id="email"
                                                        :label="$t('auth.email')"
                                                        v-model="registerForm.email"
                                                        :error="errors.email"
                                                        is-required
                                                    />
                                                </div>

                                                <div class="sm:col-span-2">
                                                    <InputComponent
                                                        id="password"
                                                        :label="$t('auth.register.password')"
                                                        v-model="registerForm.password"
                                                        :error="errors.password"
                                                        is-required
                                                        type="password"
                                                    />
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <InputComponent
                                                        id="password_confirmation"
                                                        :label="$t('auth.register.password_confirm')"
                                                        v-model="registerForm.password_confirmation"
                                                        :error="errors.password_confirmation"
                                                        is-required
                                                        type="password"
                                                    />
                                                </div>
                                            </div>
                                            <div class="mt-8">
                                                <button
                                                    type="submit"
                                                    class="w-full inline-flex justify-center rounded-md border border-black bg-primary-500 px-3 py-2 text-sm text-white shadow-sm sm:col-start-2"
                                                    @click="submitLogin"
                                                    v-text="$t('auth.login')"/>
                                            </div>
                                        </form>
                                    </div>


                                    <div class="mt-4 flex justify-center">
                                        <p class="text-sm " v-html="$t('auth.register.already_have_account')"/>
                                        <button class="text-sm btn text-primary-500 ml-2" v-text="$t('auth.register.login')" @click="registerClose"/>
                                    </div>
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
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue';
import {UserIcon, XMarkIcon} from '@heroicons/vue/20/solid';
import PillButton from '@/Components/Buttons/PillButton.vue';
import route from '@/Helpers/useRoute.js';

import {computed, ref} from 'vue';
import {useForm, usePage} from "@inertiajs/vue3";
import InputComponent from "@/Components/InputComponent.vue";

const loginModal = ref(false);
    const registerModal = ref(false);

    const registerOpen = () => {
        console.log('register')
        loginModal.value = false;
        registerModal.value = true;
        console.log(registerModal.value)
    };

    const registerClose = () => {
        registerModal.value = false;
        loginModal.value = true;
    };

    const open = () => {
        loginModal.value = true;
    };

    const close = () => {
        loginModal.value = false;
    };

    const form = useForm({
        'email': '',
        'password': '',
    })

    const registerForm = useForm({
        'first_name': '',
        'last_name': '',
        'phone': '',
        'email': '',
        'password': '',
        'password_confirmation': '',
    })

    const submitLogin = () => {
        form.post(route('login'),{
            preserveScroll: true,
            onSuccess: () => {
                close();
            }

        });
    }

    const submitRegister = () => {
        registerForm.post(route('register'),{
            preserveScroll: true,
            onSuccess: () => {
                registerClose();
            }

        });
    }
    const errors = computed(() => usePage().props.errors)
</script>
