<template>
    <div class="text-sm">
        <button
            type="button"
            class="flex items-center justify-between w-full gap-2 px-2 py-3 text-left bg-gray-50"
            :class="{ 'bg-gray-100': open }"
            @click="toggle"
        >
            <Icon v-if="icon" :icon="icon" class="w-8 h-8" />

            <div class="text-sm font-medium text-gray-900">
                <slot name="title" />
            </div>

            <ChevronDownIcon class="w-5 h-5 text-gray-400" :class="{ 'rotate-180': open }" />
        </button>

        <div v-if="open">
            <slot />
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { ChevronDownIcon } from '@heroicons/vue/24/outline';
    import Icon from '@/Components/Icon.vue';

    const props = defineProps({
        open: {
            type: Boolean,
            default: true,
        },
        icon: {
            type: [String, Function],
            default: null,
        },
    });

    const open = ref(props.open);

    const toggle = () => (open.value = !open.value);
</script>


