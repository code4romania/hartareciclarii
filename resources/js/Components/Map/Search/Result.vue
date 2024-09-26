<template>
    <button
        ref="result"
        type="button"
        class="flex flex-col gap-3 px-4 py-4 text-left"
        :class="{
            'bg-gray-100': isSelected,
            'hover:bg-gray-100 focus:bg-gray-100': !isSelected,
        }"
    >
        <div class="flex items-center gap-2">
            <Icon :icon="`services/${point.service}`" class="w-8 h-8 shrink-0" />

            <span
                class="flex-1 text-lg font-bold leading-snug break-words text-neutral-900 whitespace-break-spaces"
                v-text="point.name"
            />
        </div>

        <p class="text-sm" v-text="point.subheading" />

        <div class="flex items-start gap-2">
            <MapPinIcon class="w-5 h-5 text-gray-400 shrink-0" />

            <address class="flex-1 text-sm not-italic" v-text="point.address" />
        </div>
    </button>
</template>

<script setup>
    import { ref, computed, watch } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { MapPinIcon } from '@heroicons/vue/24/solid';
    import Icon from '@/Components/Icon.vue';

    const page = usePage();

    const props = defineProps({
        point: Object,
    });

    const result = ref(null);

    const isSelected = computed(() => page.props.point?.id === props.point.id);

    watch(isSelected, (value) => {
        if (value) {
            result.value.scrollIntoView();
        }
    });
</script>
