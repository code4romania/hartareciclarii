<template>
    <DashboardLayout>
        <div class="container grid gap-10 mx-auto mt-10">
            <div>
                <Link
                    :href="route('front.account.dashboard')"
                    :label="$t('top_menu.add_point')"
                    :icon="ArrowLongLeftIcon"
                    class="inline-flex items-center justify-start gap-2.5 font-medium select-none ring-inset whitespace-nowrap hover:opacity-75"
                >
                    <ArrowLongLeftIcon class="w-6 h-6 text-gray-500 shrink-0" />

                    <span v-text="$t('profile.back_to_my_profile')" />
                </Link>
            </div>

            <UpdateProfileForm />

            <ChangePasswordForm />
        </div>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { usePage, Link } from '@inertiajs/vue3';
    import { computed } from 'vue';
    import { ArrowLongLeftIcon } from '@heroicons/vue/20/solid';
    import Button from '@/Components/Button.vue';
    import UpdateProfileForm from '@/Components/Dashboard/UpdateProfileForm.vue';
    import ChangePasswordForm from '@/Components/Dashboard/ChangePasswordForm.vue';

    const props = defineProps({
        //
    });
</script>
