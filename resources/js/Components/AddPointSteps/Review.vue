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

        <FormField
            v-if="serviceType.can.collect_materials"
            name="materials"
            :label="$t('add_point.review.collected_materials')"
            :errors="[form.errors.materials]"
        >
            <template #default="{ invalid }">
                <Tree
                    :value="materials"
                    :invalid="invalid"
                    fluid
                    :expandedKeys="expandedKeys"
                    :pt="{
                        nodeContent: ({ global, context }) => ({
                            class: [
                                ...global.class,
                                {
                                    'bg-gray-50': !context.leaf,
                                    'pl-12': context.leaf,
                                },
                            ],
                        }),
                        nodeChildren: ({ global }) => ({
                            class: [...global.class, 'divide-y divide-gray-200'],
                        }),
                    }"
                >
                    <template #filtericon>
                        <span />
                    </template>

                    <template #category="slotProps">
                        <div class="flex items-center justify-start w-full gap-2 px-2 py-3 text-left bg-gray-50">
                            <div class="flex items-center justify-center w-8 h-8 shrink-0">
                                <img v-if="slotProps.node.icon" :src="slotProps.node.icon" alt="" />
                            </div>

                            <div class="flex-1 text-sm font-medium text-gray-900">
                                {{ slotProps.node.label }}
                            </div>
                        </div>
                    </template>
                </Tree>
            </template>
        </FormField>

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
    import Tree from 'primevue/tree';
    import Email from '@/Components/PointDetails/Email.vue';
    import FormField from '@/Components/Form/Field.vue';
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

    const materials = computed(() => {
        let selected = Object.keys(props.form.materials);

        return page.props.materials
            .map((category) => {
                category = { ...category };

                category.children = category.children.filter((material) => selected.includes(String(material.key)));

                category.selectable = false;

                return category;
            })
            .filter((category) => selected.includes(category.key));
    });

    const expandedKeys = computed(() => {
        const keys = {};

        materials.value.forEach((category) => {
            keys[category.key] = true;
        });

        return keys;
    });

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
