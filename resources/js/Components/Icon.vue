<template>
    <svg v-if="symbolId" class="fill-current" aria-hidden="true">
        <use :href="symbolId" />
    </svg>

    <component v-else :is="icon" />
</template>

<script setup>
    import { computed } from 'vue';

    const props = defineProps({
        prefix: {
            type: String,
            default: 'icon',
        },
        icon: {
            type: [String, Function],
            required: true,
        },
    });

    const symbolId = computed(() => (typeof props.icon === 'string' ? `#${props.prefix}-${props.icon}` : null));
</script>
