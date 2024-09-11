<template>
    <component
        :is="tag"
        v-bind="attributes"
        class="flex items-center font-medium select-none ring-inset whitespace-nowrap disabled:opacity-75 disabled:cursor-default"
        :class="[buttonBase, buttonSize, buttonShadow, buttonColor]"
        @click="$emit('click', $event)"
        @keydown="$emit('keydown', $event)"
    >
        <Icon v-if="icon" :icon="icon" class="shrink-0" :class="[iconSize, iconColor]" />

        <slot>{{ label }}</slot>
    </component>
</template>

<script setup>
    import { computed } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import Icon from '@/Components/Icon.vue';

    const emit = defineEmits(['click', 'keydown']);

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
        disabled: {
            type: Boolean,
            default: false,
        },
        method: {
            type: String,
            default: 'get',
            validator: (value) => ['get', 'post', 'put', 'patch', 'delete'].includes(value),
        },
        simple: {
            type: Boolean,
            default: false,
        },
    });

    const attributes = computed(() => {
        if (props.href === null) {
            return {
                type: props.type,
                disabled: props.disabled,
            };
        }

        if (props.external) {
            return {
                href: props.href,
                target: '_blank',
                rel: 'noopener noreferrer',
            };
        }

        return {
            href: props.href,
            method: props.method,
            as: props.method === 'get' ? 'a' : 'button',
        };
    });

    const tag = computed(() => {
        if (props.href === null) {
            return 'button';
        }

        if (props.external) {
            return 'a';
        }

        return Link;
    });

    const buttonBase = computed(() => ({
        'justify-start': props.simple,
        'justify-center rounded-full': !props.simple,
    }));

    const iconBase = 'shrink-0';

    const buttonSize = computed(
        () =>
            ({
                sm: 'gap-2 px-4 py-2 text-sm',
                md: 'gap-2 px-4 py-2 text-base',
                lg: 'gap-3 px-4 py-2 text-lg',
            })[props.size]
    );

    const buttonShadow = computed(() => {
        if (props.simple) {
            return null;
        }

        return {
            sm: 'shadow-sm',
            md: 'shadow',
            lg: 'shadow-lg',
        }[props.size];
    });

    const buttonColor = computed(() => {
        if (props.simple) {
            return props.primary ? 'text-primary-800' : 'text-gray-700';
        }

        return props.primary
            ? 'bg-primary-800 text-white ring-primary-800 hover:bg-primary-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-700 focus:outline-none disabled:hover:bg-primary-800'
            : 'ring-1 bg-white text-gray-700 ring-gray-300 hover:bg-gray-50 disabled:bg-gray-200 disabled:hover:bg-gray-200 ';
    });

    const iconSize = computed(
        () =>
            ({
                sm: 'h-4 w-4',
                md: 'h-5 w-5',
                lg: 'h-5 w-5',
            })[props.size]
    );

    const iconColor = computed(
        () =>
            ({
                white: 'text-gray-500',
            })[props.color]
    );
</script>
