<template>
    <template v-if="isLink">
        <a
            v-if="external"
            :href="href"
            :class="[buttonBase, buttonSize, buttonColor]"
            target="_blank"
            rel="noopener noreferrer"
        >
            <Icon v-if="icon" :icon="icon" :class="[iconBase, iconSize, iconColor]" />

            <slot>{{ label }}</slot>
        </a>

        <Link v-else :href="href" :class="[buttonBase, buttonSize, buttonColor]">
            <Icon v-if="icon" :icon="icon" :class="[iconBase, iconSize, iconColor]" />

            <slot>{{ label }}</slot>
        </Link>
    </template>

    <button v-else :type="type" :class="[buttonBase, buttonSize, buttonColor]">
        <Icon v-if="icon" :icon="icon" :class="[iconBase, iconSize, iconColor]" />

        <slot>{{ label }}</slot>
    </button>
</template>

<script setup>
    import { computed } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import Icon from '@/Components/Icon.vue';

    const props = defineProps({
        href: {
            type: String,
            default: null,
        },
        type: {
            type: String,
            default: 'button',
        },
        icon: {
            type: [String, Function],
            default: null,
        },
        external: {
            type: Boolean,
            default: false,
        },
        size: {
            type: String,
            default: 'md',
            validator: (value) => ['sm', 'md', 'lg'].includes(value),
        },
        label: {
            type: String,
            default: null,
        },
        primary: {
            type: Boolean,
            default: false,
        },
    });

    const isLink = computed(() => props.href !== null);

    const buttonBase = `flex items-center justify-center ring-inset font-medium rounded-full whitespace-nowrap `;

    const iconBase = 'shrink-0';

    const buttonSize = computed(
        () =>
            ({
                sm: 'gap-2 px-4 py-2 text-sm shadow-sm',
                md: 'gap-2 px-4 py-2 text-base shadow',
                lg: 'gap-3 px-4 py-2 text-lg shadow-lg',
            }[props.size])
    );

    const buttonColor = computed(() =>
        props.primary
            ? 'bg-primary-800 text-white ring-primary-800 hover:bg-primary-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-700 focus:outline-none'
            : 'ring-1 bg-white text-gray-700 ring-gray-300 hover:bg-gray-50'
    );

    const iconSize = computed(
        () =>
            ({
                sm: 'h-4 w-4',
                md: 'h-5 w-5',
                lg: 'h-5 w-5',
            }[props.size])
    );

    const iconColor = computed(
        () =>
            ({
                white: 'text-gray-500',
            }[props.color])
    );
</script>
