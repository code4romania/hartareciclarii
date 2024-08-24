<template>
    <LMap
        ref="map"
        :useGlobalLeaflet="true"
        :min-zoom="10"
        :max-zoom="18"
        :center="center"
        :zoom="zoom"
        @ready="ready"
        @moveend="moveend"
        :options="{
            zoomControl: false,
        }"
        class="z-0 w-full h-full"
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

        <LMarkerClusterGroup :icon-create-function="iconCreateFunction">
            <LMarker
                v-for="point in points"
                :key="point.id"
                :lat-lng="point.latlng"
                @click="() => openPoint(map.leafletObject, point)"
            >
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

    import route from '@/Helpers/useRoute.js';
    import { refreshPoints, openPoint } from '@/Helpers/useMap.js';
    import { LMap, LControl, LControlScale, LControlZoom, LIcon, LMarker, LTileLayer } from '@vue-leaflet/vue-leaflet';
    import { LMarkerClusterGroup } from 'vue-leaflet-markercluster';
    import 'leaflet.locatecontrol';

    import 'leaflet/dist/leaflet.css';
    import 'vue-leaflet-markercluster/dist/style.css';
    import 'leaflet.locatecontrol/dist/L.Control.Locate.min.css';

    import { ref, computed, watch } from 'vue';
    import { router, usePage } from '@inertiajs/vue3';

    const page = usePage();

    const points = computed(() => page.props.points);
    const center = computed(() => page.props.center);
    const zoom = computed(() => page.props.zoom);

    const props = defineProps({
        selectedPoint: {
            type: Object,
            default: null,
        },
        point: {
            type: Object,
            default: null,
        },
        bounds: {
            type: Array,
            default: null,
        },
    });

    const map = ref(null);
    const selectedPoint = computed(() => props.selectedPoint);
    const bounds = computed(() => props.bounds);
    const point = computed(() => props.point);

    const moveend = (event) => refreshPoints(event.target);

    watch(selectedPoint, (value) => {
        map.value.leafletObject.flyTo({ lat: value.lat, lng: value.lng }, 17, {
            animate: false,
        });
    });

    watch(bounds, (value) => {
        if (!value) {
            return;
        }

        var polygon = L.polygon(
            [
                [value[0], value[2]],
                [value[0], value[3]],
                [value[1], value[3]],
                [value[1], value[2]],
            ],
            { color: 'red' }
        ).addTo(map.value.leafletObject);

        // let bounds = L.latLngBounds(L.latLng(value[0], value[2]), L.latLng(value[1], value[3]));

        map.value.leafletObject.flyToBounds(polygon.getBounds(), {
            animate: false,
        });
    });

    const ready = (leafletObject) => {
        const locateControl = L.control
            .locate({
                position: 'bottomright',
                showPopup: false,
                locateOptions: {
                    enableHighAccuracy: true,
                },
                clickBehavior: {
                    inView: 'setView',
                    outOfView: 'setView',
                    inViewNotFollowing: 'setView',
                },
                onLocationError: (error) => {
                    refreshPoints(map.value.leafletObject);
                },
            })
            .addTo(leafletObject);

        locateControl.start();
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
