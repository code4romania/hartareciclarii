<template>
    <fieldset class="contents">
        <legend class="text-sm font-medium text-gray-900" v-text="$t('add_point.review.subtitle')" />

        <p class="text-xl font-medium" v-text="pointType.name" />

        <div class="text-sm font-medium">
            <p v-if="!form.administered_by" v-text="serviceType.name" />
            <p
                v-else
                v-text="
                    $t('point.service_administered', {
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

        <Materials v-if="serviceType.can.collect_materials" :materials="materials" />

        <Schedule v-if="form.schedule" :schedule="form.schedule" />

        <Observations v-if="showObservations" :observations="form.observations" :info="info" />

        <Website v-if="form.website" :website="form.website" />

        <Email v-if="form.email" :email="form.email" />

        <Phone v-if="form.phone" :phone="form.phone" />

        <FormField
            name="images"
            v-if="form.images.length"
            :label="$t('add_point.details.images')"
            :errors="[form.errors.images]"
        >
            <ul class="flex flex-wrap gap-4">
                <li
                    v-for="{ uuid, url } in form.images"
                    :key="uuid"
                    class="relative w-24 h-24 overflow-hidden rounded-lg bg-gray-50"
                >
                    <img role="presentation" alt="" :src="url" class="object-cover w-24 h-24" />
                </li>
            </ul>
        </FormField>
    </fieldset>
</template>

<script setup>
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { MapPinIcon } from '@heroicons/vue/20/solid';
    import MapPreview from '@/Components/Form/MapPreview.vue';
    import { groupMaterialsByCategory } from '@/Helpers/useMaterials.js';
    import Email from '@/Components/PointDetails/Email.vue';
    import FormField from '@/Components/Form/Field.vue';
    import Materials from '@/Components/PointDetails/Materials.vue';
    import Observations from '@/Components/PointDetails/Observations.vue';
    import Phone from '@/Components/PointDetails/Phone.vue';
    import Schedule from '@/Components/PointDetails/Schedule.vue';
    import Website from '@/Components/PointDetails/Website.vue';

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

    const page = usePage();

    const materials = computed(() =>
        groupMaterialsByCategory(
            page.props.materials.items.filter((material) => props.form.materials.includes(material.id))
        )
    );

    const showObservations = computed(() => props.form.observations !== null || Object.keys(info.value).length > 0);

    const info = computed(() => {
        const fields = {};
        const fieldNames = ['free_of_charge', 'offers_money', 'offers_transport', 'offers_vouchers'];

        fieldNames.forEach((fieldName) => {
            if ([null, -1].includes(props.form[fieldName])) {
                return;
            }

            fields[fieldName] = props.form[fieldName];
        });

        return fields;
    });
</script>
