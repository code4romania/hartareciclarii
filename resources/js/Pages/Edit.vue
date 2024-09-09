<template>
    <DashboardLayout>
        <div class="container ms-16 pt-28">
            <div class="w-1/4 mb-10 start-0">
                <Link :href="route('frontaccount.dashboard')" class="flex items-center justify-items-center">
                    <ArrowLongLeftIcon class="w-8 h-8 mr-2" />
                    <p v-text="$t('profile.back_to_my_profile')" class="text-lg" />
                </Link>
            </div>
            <div class="w-1/4 mb-6 start-0">
                <p v-text="$t('profile.personal_info')" class="text-lg" />
            </div>
            <div class="w-1/2 overflow-hidden bg-white divide-y divide-gray-200 rounded-lg shadow">
                <div class="px-4 py-5 sm:p-6">
                    <form @submit="updateProfile" class="mt-8">
                        <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                            <InputComponent
                                id="first-name"
                                :label="$t('auth.register.first_name')"
                                v-model="updateProfile.first_name"
                                :error="errors.first_name"
                                is-required
                            />
                            <InputComponent
                                id="last-name"
                                :label="$t('auth.register.last_name')"
                                v-model="updateProfile.last_name"
                                :error="errors.last_name"
                                is-required
                            />
                            <div class="">
                                <InputComponent
                                    id="phone"
                                    :label="$t('auth.register.phone')"
                                    v-model="updateProfile.phone"
                                    :error="errors.phone"
                                    type="tel"
                                />
                            </div>
                            <div class="">
                                <InputComponent
                                    id="email"
                                    :label="$t('auth.email')"
                                    v-model="updateProfile.email"
                                    :error="errors.email"
                                    type="email"
                                    is-required
                                />
                            </div>
                            <div class="">
                                <CheckboxComponent
                                    id="subscribe-newsletter"
                                    :label="$t('auth.register.subscribe_to_newsletter')"
                                    v-model="updateProfile.send_newsletter"
                                    :checked="updateProfile.send_newsletter"
                                    :error="errors.send_newsletter"
                                />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="px-4 py-4 sm:px-6 bg-gray-50">
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex justify-center px-3 py-2 text-sm text-white border border-black shadow-sm rounded-xl bg-primary-500 sm:col-start-2"
                            @click="submitUpdateProfile"
                            v-text="$t('profile.save')"
                        />
                    </div>
                </div>
            </div>
            <div class="w-1/4 mt-10 mb-6 start-0">
                <p v-text="$t('profile.change_password')" class="text-lg" />
            </div>
            <div class="w-1/2 mb-6 overflow-hidden bg-white divide-y divide-gray-200 rounded-lg shadow">
                <div class="px-4 py-5 sm:p-6">
                    <form @submit="updatePassword" class="mt-8">
                        <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-">
                            <InputComponent
                                id="current-password"
                                :label="$t('profile.current_password')"
                                v-model="updatePassword.current_password"
                                :error="errors.current_password"
                                type="password"
                            />
                            <InputComponent
                                id="password"
                                :label="$t('profile.new_password')"
                                v-model="updatePassword.password"
                                :error="errors.password"
                                type="password"
                                is-required
                            />
                            <InputComponent
                                id="email"
                                :label="$t('profile.new_password_confirm')"
                                v-model="updatePassword.password_confirmation"
                                :error="errors.password_confirmation"
                                type="password"
                                is-required
                            />
                        </div>
                    </form>
                </div>
                <div class="px-4 py-4 sm:px-6 bg-gray-50">
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex justify-center px-3 py-2 text-sm text-white border border-black shadow-sm rounded-xl bg-primary-500 sm:col-start-2"
                            @click="submitUpdatePassword"
                            v-text="$t('profile.save')"
                        />
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { usePage, Link, useForm } from '@inertiajs/vue3';
    import { computed } from 'vue';
    import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline/index.js';
    import InputComponent from '@/Components/InputComponent.vue';
    import CheckboxComponent from '@/Components/CheckboxComponent.vue';
    import route from '@/Helpers/useRoute';

    const user = usePage().props.auth;
    const props = defineProps({
        profile: {
            type: Object,
            required: true,
        },
    });

    const updateProfile = useForm({
        first_name: props.first_name,
        last_name: props.last_name,
        phone: props.phone,
        email: props.email,
        send_newsletter: props.send_newsletter,
    });

    const updatePassword = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });
    const errors = computed(() => usePage().props.errors);

    const submitUpdateProfile = () => {
        updateProfile.post(route('profile.update'));
    };

    const submitUpdatePassword = () => {
        updatePassword.post(route('profile.change-password'));
    };
</script>
