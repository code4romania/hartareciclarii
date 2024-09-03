<template>
    <FormField
        name="location"
        :errors="[form.errors.location, form.errors['location.lat'], form.errors['location.lng']]"
    >
        <div class="w-full aspect-w-16 aspect-h-9">
            <LMap
                v-if="form.location.lat && form.location.lng"
                ref="map"
                :zoom="18"
                :center="[form.location.lat, form.location.lng]"
                @ready="ready"
                :options="{
                    attributionControl: false,
                    zoomControl: false,
                    boxZoom: false,
                    doubleClickZoom: false,
                    dragging: false,
                    keyboard: false,
                    scrollWheelZoom: false,
                    tapHold: false,
                    touchZoom: false,
                }"
                class="z-0 w-full h-full"
            >
                <LControl v-if="$slots.bottomleft" position="bottomleft">
                    <slot name="bottomleft" />
                </LControl>

                <LControl v-if="$slots.bottomright" position="bottomright">
                    <slot name="bottomright" />
                </LControl>

                <LTileLayer
                    url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png"
                    layer-type="base"
                    subdomains="abcd"
                    name="OpenStreetMap"
                    attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                />

                <LMarker ref="marker" :lat-lng="form.location" />
            </LMap>

            <div v-else class="flex items-center overflow-hidden bg-gray-400 justify-stretch">
                <img src="../../../images/map/placeholder.png" class="absolute inset-0 z-0 object-cover select-none" />

                <div
                    class="relative flex-1 px-6 py-4 text-sm bg-white bg-opacity-60"
                    v-text="$t('add_point.type.map_placeholder')"
                />
            </div>
        </div>
    </FormField>
</template>

<script setup>
    import { ref, watch } from 'vue';
    import { LMap, LControl, LMarker, LTileLayer } from '@vue-leaflet/vue-leaflet';
    import FormField from '@/Components/Form/Field.vue';
    import Button from '@/Components/Button.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
        editable: {
            type: Boolean,
            default: false,
        },
        ready: {
            type: Function,
            default: () => {},
        },
    });

    const marker = ref(null);

    watch(
        () => props.form.location,
        ({ lat, lng }) => {
            marker.value.leafletObject.setLatLng({ lat, lng });
        }
        // { deep: true }
    );
</script>

