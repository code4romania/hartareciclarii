<template>
    <div class="grid grid-flow-col gap-4">
        <div class="col-span-6 overflow-hidden bg-white rounded-lg shadow">
            <div class="p-6 bg-white">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <div class="sm:flex sm:space-x-5">
                        <div class="flex-shrink-0">
                            <img class="w-20 h-20 mx-auto rounded-full" :src="$page.props.auth.imageUrl" alt="" />
                        </div>
                        <div class="mt-4 text-center sm:mt-0 sm:pt-1 sm:text-left">
                            <p class="text-xl font-bold text-gray-900 sm:text-2xl">{{ $page.props.auth.full_name }}</p>
                            <p class="text-sm font-medium text-gray-600">
                                {{ $page.props.auth.email }} | {{ $page.props.auth.phone }}
                            </p>
                            <p class="text-sm font-medium text-gray-600">
                                {{ $t('profile.register_from') + $page.props.auth.created_at }}
                            </p>
                        </div>
                    </div>
                    <div class="grid gap-2 mt-5 sm:mt-0">
                        <Link
                            href="route('profile.edit')"
                            class="flex items-center justify-center px-3 py-2 text-sm font-semibold text-gray-900 bg-white rounded-md shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                            v-text="$t('profile.settings')"
                        />
                        <Link
                            href="route('logout')"
                            class="flex items-center justify-center px-3 py-2 text-sm font-semibold text-gray-900 bg-white rounded-md shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                            v-text="$t('profile.logout')"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="flex col-span-2 bg-[#E3F1E6] rounded-2xl">
            <div class="w-1/3 p-6">
                <Icon icon="defaultPoint" class="w-16 h-16" />
            </div>
            <div class="p-6">
                <h2 class="text-2xl font-bold text-primary-500">{{ contributions_count }}</h2>
                <p class="text-primary-500" v-text="$t('profile.contributions')"></p>
            </div>
        </div>
    </div>
</template>
<script setup>
    import { usePage, Link } from '@inertiajs/vue3';
    import Icon from '@/Components/Icon.vue';

    const user = usePage().props.user;

    const props = defineProps({
        contributions_count: {
            type: Number,
            required: true,
        },
    });
</script>
