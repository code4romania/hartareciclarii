<template>
    <Head :title="title" />

    <div class="flex flex-col min-h-screen sm:flex-row">
        <div class="flex flex-col justify-center flex-1 px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="w-full max-w-sm mx-auto lg:w-96">
                <Link :href="route('front.map.index')">
                    <Icon icon="logo" class="w-24 h-8 md:w-32 md:h-12 shrink-0" />
                </Link>

                <h2
                    class="mt-8 text-2xl font-semibold leading-9 tracking-tight text-gray-900 sm:text-3xl"
                    v-text="title"
                />

                <div class="mt-10">
                    <Alert
                        v-if="$page.props.flash"
                        :type="$page.props.flash.success ? 'success' : 'error'"
                        :message="$page.props.flash.message"
                        class="mb-6"
                    />

                    <slot />
                </div>
            </div>
        </div>

        <div
            class="flex flex-col items-center justify-center flex-1 px-4 py-12 sm:px-6 lg:px-20 xl:px-24 bg-primary-700"
        >
            <div class="prose-sm prose text-white sm:prose-base prose-headings:text-white">
                <slot name="backdrop" />
            </div>
        </div>
    </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';
    import Alert from '@/Components/Alert.vue';
    import Head from '@/Components/Head.vue';
    import Icon from '@/Components/Icon.vue';

    defineProps({
        title: {
            type: String,
            required: true,
        },
    });
</script>
