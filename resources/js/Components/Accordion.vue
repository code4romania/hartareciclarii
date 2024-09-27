<template>
    <component :is="as" class="text-sm">
        <button
            type="button"
            class="flex items-center justify-start w-full gap-2 text-left"
            :class="{
                'py-1': simple,
                'px-2 py-3 bg-gray-50': !simple,
                'bg-gray-100': open && !simple,
            }"
            @click="toggle"
        >
            <div v-if="!simple" class="flex items-center justify-center w-8 h-8 shrink-0">
                <slot name="icon" />
            </div>

            <div class="flex-1 text-sm font-medium text-gray-900">
                <slot name="title" />
            </div>

            <ChevronDownIcon
                class="w-5 h-5 text-gray-500 shrink-0"
                :class="{
                    'text-gray-500': !simple,
                    'text-gray-900': simple,
                    'rotate-180': open,
                }"
            />
        </button>

        <component :is="['ul', 'ol'].includes(as) ? 'li' : 'div'" v-if="open">
            <slot />
        </component>
    </component>
</template>

<script setup>
    import { ref } from 'vue';
    import { ChevronDownIcon } from '@heroicons/vue/24/outline';

    const props = defineProps({
        open: {
            type: Boolean,
            default: true,
        },
        simple: {
            type: Boolean,
            default: false,
        },
        as: {
            type: [Object, String],
            default: 'div',
        },
    });

    const open = ref(props.open);

    const toggle = () => (open.value = !open.value);
</script>
