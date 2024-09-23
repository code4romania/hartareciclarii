<template>
    <TransitionRoot
        :show="show"
        as="template"
        enter="transition-opacity duration-75"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="transition-opacity duration-150"
        leave-from="opacity-100"
        leave-to="opacity-0"
    >
        <div
            class="absolute"
            :class="{
                'top-full pt-2': position === 'bottom',
                'bottom-full pb-2': position === 'top',
                'left-full pl-2': position === 'right',
                'right-full pr-2': position === 'left',
            }"
        >
            <div
                class="relative p-3 leading-tight text-white break-words whitespace-pre-line bg-gray-500 rounded-md shadow-md"
                :class="{
                    'before:w-4 before:h-4 before:rotate-45 before:bg-gray-500 before:absolute before:z-0': arrow,
                    'before:-bottom-2 before:left-0 before:right-0 before:mx-auto': arrow && position === 'top',
                    'before:-top-2 before:left-0 before:right-0 before:mx-auto': arrow && position === 'bottom',
                    'before:-left-2 before:top-0 before:bottom-0 before:my-auto': arrow && position === 'right',
                    'before:-right-2 before:top-0 before:bottom-0 before:my-auto': arrow && position === 'left',
                }"
                v-text="text"
            />
        </div>
    </TransitionRoot>
</template>

<script setup>
    import { TransitionRoot } from '@headlessui/vue';

    const props = defineProps({
        position: {
            type: String,
            default: 'top',
        },
        text: {
            type: String,
            required: true,
        },
        show: {
            type: Boolean,
            default: false,
        },
        arrow: {
            type: Boolean,
            default: false,
        },
    });
</script>
