<template>
    <Head :title="title" />

    <div class="flex flex-col min-h-screen sm:flex-row">
        <div class="flex flex-col justify-center flex-1 px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="w-full max-w-sm mx-auto lg:w-96">
                <h2
                    class="text-2xl font-semibold leading-9 tracking-tight text-center text-gray-900 sm:text-3xl"
                    v-text="title"
                />

                <div class="mt-10">
                    <slot />
                </div>
            </div>
        </div>

        <div class="flex flex-col justify-center flex-1 px-4 py-12 sm:px-6 lg:px-20 xl:px-24 bg-primary-700">
            <slot name="backdrop" />
        </div>
    </div>
</template>

<script setup>
    import Head from '@/Components/Head.vue';

    defineProps({
        title: {
            type: String,
            required: true,
        },
    });
</script>
