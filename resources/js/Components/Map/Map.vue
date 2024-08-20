<template>
    <LMap
        ref="map"
        :useGlobalLeaflet="true"
        :min-zoom="10"
        :zoom="10"
        :max-zoom="18"
        :center="[45.9432, 24.9668]"
        @ready="ready"
        @moveend="moveend"
        :options="{
            zoomControl: false,
        }"
        class="z-0 w-full h-full"
    >
        <LControlZoom position="bottomright" />
        <LTileLayer
            url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png"
            layer-type="base"
            subdomains="abcd"
            name="OpenStreetMap"
            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
        />
        <LMarkerClusterGroup>
            <LMarker v-for="point in points" :key="point.id" :lat-lng="point.latlng" @click="show(point)" />
        </LMarkerClusterGroup>
    </LMap>
</template>

<script setup>
    import L from 'leaflet';
    globalThis.L = L;

    import { LMap, LTileLayer, LMarker, LControlZoom } from '@vue-leaflet/vue-leaflet';
    import { LMarkerClusterGroup } from 'vue-leaflet-markercluster';

    import 'leaflet/dist/leaflet.css';
    import 'vue-leaflet-markercluster/dist/style.css';

    import { ref, computed, watch } from 'vue';
    import { router, usePage } from '@inertiajs/vue3';
    import { useGeolocation } from '@vueuse/core';

    const points = computed(() => usePage().props.points?.data || []);

    const props = defineProps({
        selectedPoint: {
            type: Object,
            default: null,
        },
        point: {
            type: Object,
            default: null,
        },
    });

    const map = ref(null);
    const selectedPoint = computed(() => props.selectedPoint);
    const point = computed(() => props.point);

    const moveend = (event) => {
        const bounds = event.target.getBounds();
        const center = event.target.getCenter();

        router.reload({
            data: {
                bounds: bounds.toBBoxString(),
                center: `${center.lat},${center.lng}`,
            },
            only: ['points', 'search_results'],
        });
    };

    watch(selectedPoint, (value) => {
        map.value.leafletObject.flyTo({ lat: value.lat, lng: value.lng }, 17, {
            animate: false,
        });
    });

    const ready = (leafletObject) => {
        const { coords, locatedAt, error, resume, pause } = useGeolocation({
            enableHighAccuracy: true,
        });

        watch(locatedAt, () => {
            pause();

            leafletObject.flyTo([coords.value.latitude, coords.value.longitude], 17, {
                animate: false,
            });
        });

        watch(error, () => {
            let coords = leafletObject.getCenter();

            leafletObject.flyTo([coords.lat, coords.lng], 10, {
                animate: false,
            });
        });
    };

    function show(point) {
        router.visit(`/point/${point.id}`, {
            data: {
                bounds: new URLSearchParams(window.location.search).get('bounds'),
                center: new URLSearchParams(window.location.search).get('center'),
            },
        });
    }
</script>
