<template>
    <div
        class="flex flex-col overflow-hidden bg-gray-100"
        :class="{
            'h-screen': !dashboard,
            'min-h-screen ': dashboard,
        }"
    >
        <EmailVerifiedAlert v-if="$page.props.auth?.is_unverified || $page.props.auth?.show_verified_message" />

        <Header class="shrink-0" :dashboard="dashboard" />

        <div class="relative flex-1">
            <slot />
        </div>
    </div>
</template>

<script setup>
    import { computed } from 'vue';
    import route from '@/Helpers/useRoute';
    import EmailVerifiedAlert from '@/Components/EmailVerifiedAlert.vue';
    import Header from '@/Components/Navigation/Header.vue';
    import { usePage } from '@inertiajs/vue3';

    const page = usePage();

    const dashboard = computed(() => page.url && route().current('front.account.*'));
</script>
