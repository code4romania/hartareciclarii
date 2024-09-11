<template>
    <fieldset class="contents">
        <legend class="text-sm font-medium text-gray-900 sr-only" v-text="$t('add_point.type.change_pin_location')" />

        <FormField
            name="location"
            :errors="[form.errors.location, form.errors['location.lat'], form.errors['location.lng']]"
        >
            <LMap
                ref="map"
                :zoom="18"
                :center="[form.location.lat, form.location.lng]"
                :max-bounds="maxBounds"
                :max-bounds-viscosity="1.0"
                @ready="ready"
                :options="{
                    attributionControl: false,
                    zoomControl: false,
                }"
                class="z-0 w-full aspect-w-15 aspect-h-16"
            >
                <LControlZoom position="bottomright" />

                <LControlScale position="bottomleft" :imperial="false" />

                <LTileLayer
                    url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png"
                    layer-type="base"
                    subdomains="abcd"
                    name="OpenStreetMap"
                    attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                />

                <LMarker ref="marker" :lat-lng="form.location" @dragend="droppedMarker" draggable />
            </LMap>
        </FormField>
    </fieldset>
</template>

<script setup>
    import { inject } from 'vue';
    import { LMap, LControlScale, LControlZoom, LMarker, LTileLayer } from '@vue-leaflet/vue-leaflet';
    import useLocate from '@/Helpers/useLocate.js';
    import { reverse } from '@/Helpers/useReverse.js';

    import FormField from '@/Components/Form/Field.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
    });

    const maxBounds = inject('max_map_bounds');

    const { locateControl } = useLocate();

    const updatePoint = (lat, lng) => {
        props.form.location.lat = lat;
        props.form.location.lng = lng;
    };

    const ready = (leafletObject) => {
        updatePoint(leafletObject.getCenter().lat, leafletObject.getCenter().lng);

        locateControl().addTo(leafletObject);
    };

    const droppedMarker = async (event) => {
        updatePoint(event.target.getLatLng().lat, event.target.getLatLng().lng);

        props.form.address_override = await reverse(event.target.getLatLng());
    };
</script>
