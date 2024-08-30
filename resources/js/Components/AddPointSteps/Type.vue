<template>
    <fieldset class="contents">
        <legend class="text-sm font-medium text-gray-900" v-text="$t('add_point.type.subtitle')" />

        <template v-if="!changingPinLocation">
            <Select
                name="service_type"
                :label="$t('add_point.type.service_type')"
                :placeholder="$t('add_point.type.service_type_placeholder')"
                v-model="form.service_type"
                :errors="[form.errors.service_type]"
                :options="serviceTypes"
                required
            />

            <FormField
                name="address"
                :label="$t('add_point.type.exact_address')"
                :errors="[form.errors.address]"
                required
            >
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

            <div class="flex justify-end">
                <button
                    type="button"
                    class="text-sm font-medium text-primary-800 hover:underline"
                    v-text="$t('add_point.type.use_my_current_location')"
                    @click="getCurrentLocation"
                />
            </div>
        </template>

        <FormField
            name="location"
            :errors="[form.errors.location, form.errors['location.lat'], form.errors['location.lng']]"
        >
            <div
                class="w-full"
                :class="{
                    'aspect-w-16 aspect-h-9': !changingPinLocation,
                    'aspect-w-15 aspect-h-16': changingPinLocation,
                }"
            >
                <LMap
                    v-if="form.location.lat && form.location.lng"
                    ref="map"
                    :zoom="18"
                    :center="[form.location.lat, form.location.lng]"
                    @ready="ready"
                    @click="moveMarker"
                    :options="{
                        attributionControl: false,
                        zoomControl: false,
                        dragging: false,
                    }"
                    class="z-0 w-full h-full"
                >
                    <LControl v-if="!changingPinLocation" position="bottomleft">
                        <Button
                            size="sm"
                            :label="$t('add_point.type.change_pin_location')"
                            @click="$emit('changePinLocation')"
                        />
                    </LControl>

                    <LTileLayer
                        url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png"
                        layer-type="base"
                        subdomains="abcd"
                        name="OpenStreetMap"
                        attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                    />

                    <LMarker
                        ref="marker"
                        :lat-lng="form.location"
                        :draggable="changingPinLocation"
                        @dragend="droppedMarker"
                    />
                </LMap>

                <div v-else class="flex items-center overflow-hidden bg-gray-400 justify-stretch">
                    <img
                        src="../../../images/map/placeholder.png"
                        class="absolute inset-0 z-0 object-cover select-none"
                    />

                    <div
                        class="relative flex-1 px-6 py-4 text-sm bg-white bg-opacity-60"
                        v-text="$t('add_point.type.map_placeholder')"
                    />
                </div>
            </div>
        </FormField>
    </fieldset>
</template>

<script setup>
    import axios from 'axios';
    import { computed, ref, watch, nextTick } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { useDebounceFn } from '@vueuse/core';
    import { LMap, LControl, LMarker, LTileLayer } from '@vue-leaflet/vue-leaflet';
    import route from '@/Helpers/useRoute.js';
    import useLocate from '@/Helpers/useLocate.js';

    import Button from '@/Components/Button.vue';
    import Modal from '@/Components/Modal.vue';
    import FormField from '@/Components/Form/Field.vue';
    import Select from '@/Components/Form/Select.vue';
    import AutoComplete from 'primevue/autocomplete';

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
        changingPinLocation: {
            type: Boolean,
            required: false,
        },
    });

    const emit = defineEmits(['changePinLocation']);

    const showMap = ref(false);
    const changingPinLocation = ref(false);
    const map = ref(null);
    const marker = ref(null);
    const address = ref(null);
    const point = ref(null);

    const { locateControl, startLocate } = useLocate();

    const updatePoint = (lat, lng, setView = false) => {
        props.form.location.lat = lat;
        props.form.location.lng = lng;

        showMap.value = true;
    };

    watch(address, (address) => {
        if (typeof address === 'string') {
            props.form.address = address;

            return;
        }

        props.form.address = address.name;

        updatePoint(address.center[0], address.center[1], true);
    });

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

    const moveMarker = (event) => {
        if (!changingPinLocation.value) {
            return;
        }

        // TODO: fix this
        updatePoint(event.latlng.lat, event.latlng.lng);
    };

    const droppedMarker = (event) => {
        if (!changingPinLocation.value) {
            return;
        }
        updatePoint(event.target.getLatLng().lat, event.target.getLatLng().lng, true);
    };

    const ready = (leafletObject) => {
        updatePoint(leafletObject.getCenter().lat, leafletObject.getCenter().lng, false);

        locateControl().addTo(leafletObject);
    };

    watch(changingPinLocation, (value) => {
        if (value) {
            map.value.leafletObject.dragging.enable();
        } else {
            map.value.leafletObject.dragging.disable();
        }
    });
</script>
