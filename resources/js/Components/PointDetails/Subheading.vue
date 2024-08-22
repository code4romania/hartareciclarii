<template>
    <div class="p-6">
        <p v-if="subheading" class="mb-4 text-sm" v-text="subheading" />

        <div class="inline-flex items-center gap-1">
            <Icon
                :icon="statusIcon"
                class="w-4 h-4 shrink-0"
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
        </div>
    </div>
</template>

<script setup>
    import { computed, defineProps } from 'vue';
    import { ExclamationTriangleIcon, QuestionMarkCircleIcon, CheckCircleIcon } from '@heroicons/vue/16/solid';
    import Icon from '@/Components/Icon.vue';

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
</script>
