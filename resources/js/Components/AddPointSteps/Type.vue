<template>
    <fieldset class="contents">
        <legend class="text-sm font-medium text-gray-900" v-text="$t('add_point.type.subtitle')" />

        <Select
            name="service_type"
            :label="$t('add_point.type.service_type')"
            :placeholder="$t('add_point.type.service_type_placeholder')"
            v-model="form.service_type_id"
            :errors="[form.errors.service_type_id]"
            :options="serviceTypes"
            required
        />

        <FormField name="address" :label="$t('add_point.type.exact_address')" :errors="[form.errors.address]" required>
            <template #default="{ invalid }">
                <AutoComplete
                    v-model="address"
                    class="w-full"
                    :placeholder="$t('add_point.type.exact_address_placeholder')"
                    :suggestions="suggestions"
                    @complete="complete"
                    :invalid="invalid"
                    optionLabel="name"
                    fluid
                >
                    <template #option="slotProps">
                        <div class="flex w-full gap-2 text-sm text-left">
                            <MapPinIcon class="w-5 h-5 fill-gray-400" />

                            <span class="flex-1 truncate" v-text="slotProps.option.name" />
                        </div>
                    </template>
                </AutoComplete>
            </template>
        </FormField>

        <UseGeolocation class="flex justify-end" @located="located" />

        <MapPreview :form="form" editable @ready="ready">
            <template #bottomleft>
                <Button
                    size="sm"
                    :label="$t('add_point.type.change_pin_location')"
                    @click="$emit('changePinLocation')"
                />
            </template>
        </MapPreview>
    </fieldset>
</template>

<script setup>
    import axios from 'axios';
    import { computed, ref, watch, nextTick } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { useDebounceFn } from '@vueuse/core';
    import { LMap, LControl, LMarker, LTileLayer } from '@vue-leaflet/vue-leaflet';
    import AutoComplete from 'primevue/autocomplete';
    import route from '@/Helpers/useRoute.js';
    import { reverse } from '@/Helpers/useReverse.js';

    import Button from '@/Components/Button.vue';
    import Modal from '@/Components/Modal.vue';
    import FormField from '@/Components/Form/Field.vue';
    import MapPreview from '@/Components/Form/MapPreview.vue';
    import Select from '@/Components/Form/Select.vue';
    import UseGeolocation from '@/Components/UseGeolocation.vue';

    import { MapPinIcon } from '@heroicons/vue/16/solid';

    const page = usePage();

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
        serviceTypes: {
            type: Array,
            required: true,
        },
    });

    const emit = defineEmits(['changePinLocation']);

    const address = computed({
        get: () => props.form.address,
        set: (address) => {
            if (typeof address === 'string') {
                props.form.address = address;

                return;
            }

            props.form.address = address.name;
            props.form.city = address.city;
            props.form.county = address.county;

            updatePoint(address.center[0], address.center[1]);
        },
    });

    const located = async ({ lat, lng }) => {
        updatePoint(lat, lng);

        props.form.address = await reverse({ lat, lng });
    };

    const updatePoint = (lat, lng) => {
        props.form.location.lat = lat;
        props.form.location.lng = lng;
    };

    const suggestions = ref([]);

    const complete = useDebounceFn(() => {
        axios
            .get(
                route('front.map.suggest', {
                    query: address.value,
                    type: 'location',
                })
            )
            .then((response) => {
                if (Array.isArray(response.data)) {
                    suggestions.value = response.data;
                }
            });
    }, 500);

    const ready = (leafletObject) => {
        const center = leafletObject.getCenter();

        updatePoint(center.lat, center.lng);
    };
</script>
