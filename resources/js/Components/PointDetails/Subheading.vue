<template>
    <div class="p-6 pt-2">
        <p v-if="subheading" class="mb-4 text-sm" v-text="subheading" />

        <div
            ref="badge"
            class="inline-flex flex-wrap items-center gap-1 px-2 py-1 transition-colors duration-75"
            :class="{
                'cursor-default hover:bg-gray-100 rounded-full': tooltip,
            }"
        >
            <Icon
                :icon="statusIcon"
                class="w-4 h-4 shrink-0 -ms-0.5"
                :class="{
                    'text-green-500': status.color === 'success',
                    'text-yellow-500': status.color === 'warning',
                    'text-red-500': status.color === 'danger',
                }"
            />

            <span
                class="text-sm font-medium"
                :class="{
                    'text-green-900': status.color === 'success',
                    'text-yellow-900': status.color === 'warning',
                    'text-red-900': status.color === 'danger',
                }"
                v-text="status.label"
            />

            <div v-if="tooltip" class="absolute h-7">
                <Tooltip :show="showTooltip" position="bottom" :text="tooltip" class="w-60" arrow />
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, watch } from 'vue';
    import { trans } from 'laravel-vue-i18n';
    import { ExclamationTriangleIcon, QuestionMarkCircleIcon, CheckCircleIcon } from '@heroicons/vue/16/solid';
    import Icon from '@/Components/Icon.vue';
    import Tooltip from '@/Components/Tooltip.vue';
    import { useElementHover } from '@vueuse/core';

    const props = defineProps({
        subheading: {
            type: String,
            required: true,
        },
        status: {
            type: Object,
            required: true,
            validator: (value) => ['icon', 'color', 'label'].every((el) => value.hasOwnProperty(el)),
        },
    });

    const statusIcon = computed(() => {
        return {
            'heroicon-m-check-circle': CheckCircleIcon,
            'heroicon-m-question-mark-circle': QuestionMarkCircleIcon,
            'heroicon-m-exclamation-triangle': ExclamationTriangleIcon,
        }[props.status.icon];
    });

    const tooltip = computed(() => {
        if (!props.status.problems.length) {
            return null;
        }

        return trans('report.count', { problems: props.status.problems.join(', ') });
    });

    const badge = ref(null);
    const showTooltip = ref(false);

    const hovered = useElementHover(badge);

    watch(hovered, (hovered) => (showTooltip.value = hovered));
</script>
