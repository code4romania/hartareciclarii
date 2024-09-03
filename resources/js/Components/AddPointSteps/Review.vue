<template>
    <fieldset class="contents">
        <legend class="text-sm font-medium text-gray-900" v-text="$t('add_point.review.subtitle')" />

        <p class="text-xl font-medium" v-text="pointType.name" />

        <div class="text-sm font-medium">
            <p v-if="!form.administered_by" v-text="serviceType.name" />
            <p
                v-else
                v-text="
                    $t('add_point.review.service_administered', {
                        service: serviceType.name,
                        administrator: form.administered_by,
                    })
                "
            />
        </div>

        <div class="flex items-start gap-2">
            <MapPinIcon class="w-5 h-5 text-gray-400 shrink-0" />

            <address class="flex-1 text-sm not-italic" v-text="form.address" />
        </div>

        <MapPreview :form="form" />
    </fieldset>
</template>

<script setup>
    import { MapPinIcon } from '@heroicons/vue/20/solid';
    import MapPreview from '@/Components/Form/MapPreview.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
        serviceType: {
            type: Object,
            required: true,
        },
        pointType: {
            type: Object,
            required: true,
        },
    });
</script>
