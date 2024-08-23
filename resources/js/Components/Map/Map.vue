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

        <LMarkerClusterGroup :icon-create-function="iconCreateFunction">
            <LMarker v-for="point in points" :key="point.id" :lat-lng="point.latlng" @click="show(point)">
                <LIcon
                    v-if="isCurrentPoint(point)"
                    :icon-url="getMapPinIcon(point, 'lg')"
                    :icon-size="[32, 43]"
                    :icon-anchor="[16, 43]"
                />
                <LIcon v-else :icon-url="getMapPinIcon(point, 'sm')" :icon-size="[32, 32]" :icon-anchor="[16, 16]" />
            </LMarker>
        </LMarkerClusterGroup>
    </LMap>
</template>

<script setup>
    import L from 'leaflet';
    globalThis.L = L;

    import { LMap, LTileLayer, LMarker, LControlZoom, LIcon } from '@vue-leaflet/vue-leaflet';
    import { LMarkerClusterGroup } from 'vue-leaflet-markercluster';

    import 'leaflet/dist/leaflet.css';
    import 'vue-leaflet-markercluster/dist/style.css';

    import { ref, computed, watch } from 'vue';
    import { router, usePage } from '@inertiajs/vue3';
    import { useGeolocation } from '@vueuse/core';

    const page = usePage();

    const points = computed(() => page.props.points || []);

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

    const show = (point) => {
        router.visit(`/point/${point.id}`, {
            data: {
                bounds: new URLSearchParams(window.location.search).get('bounds'),
                center: new URLSearchParams(window.location.search).get('center'),
            },
        });
    };

    const getMapPinIcon = (point, size) => page.props.icons[point.service][size];

    const isCurrentPoint = (point) => {
        if (page.props?.type !== 'point') {
            return false;
        }

        return point.id === page.props.point.id;
    };

    const iconCreateFunction = (cluster) =>
        new L.Icon({
            iconUrl: page.props.icons.markercluster,

            iconSize: [32, 32], // size of the icon
            iconAnchor: [16, 16], // point of the icon which will correspond to marker's location
        });
</script>
