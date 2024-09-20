<template>
    <div class="relative flex flex-1 overflow-hidden">
        <!-- Static sidebar for desktop -->
        <Sidebar class="hidden border-r border-gray-200 lg:flex w-80" @select-point="selectPoint" />

        <div class="relative flex-1">
            <div
                class="absolute inset-0 z-10 flex flex-col gap-4 overflow-hidden pointer-events-none lg:inset-3 lg:left-6 lg:top-4 lg:right-auto lg:w-96"
            >
                <div
                    class="relative flex gap-4 px-4 py-3 bg-white shadow pointer-events-auto lg:bg-transparent lg:p-0 lg:shadow-none"
                >
                    <Search class="relative flex-1" :map="map" @locate="locate" />

                    <button
                        type="button"
                        @click="sidebarOpen = !sidebarOpen"
                        v-text="$t('sidebar.filters')"
                        class="font-medium uppercase appearance-none text-primary-800 lg:hidden"
                    />
                </div>

                <Sidebar
                    v-if="sidebarOpen"
                    class="flex flex-1 w-full -mt-4 pointer-events-auto lg:hidden"
                    @select-point="selectPoint"
                />

                <slot :map="map" />
            </div>

            <LMap
                ref="map"
                :min-zoom="8"
                :max-zoom="18"
                :center="mapOptions.center"
                :zoom="mapOptions.zoom"
                :max-bounds="maxBounds"
                :max-bounds-viscosity="1.0"
                @ready="ready"
                @movestart="cancelMapVisits"
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
                        :key="`${point.id}-${point.current ? 'current' : 'default'}`"
                        :lat-lng="point.latlng"
                        @click="() => selectPoint(point.id)"
                    >
                        <LIcon
                            v-if="point.current"
                            :icon-url="getMapPinIcon(point, 'lg')"
                            :icon-size="[32, 43]"
                            :icon-anchor="[16, 43]"
                        />

                        <LIcon
                            v-else
                            :icon-url="getMapPinIcon(point, 'sm')"
                            :icon-size="[32, 32]"
                            :icon-anchor="[16, 16]"
                        />
                    </LMarker>
                </LMarkerClusterGroup>
            </LMap>
        </div>
    </div>
</template>

<script setup>
    import L from 'leaflet';

    import { XMarkIcon } from '@heroicons/vue/24/outline';

    import { LMap, LControlScale, LControlZoom, LIcon, LMarker, LTileLayer } from '@vue-leaflet/vue-leaflet';
    import { LMarkerClusterGroup } from 'vue-leaflet-markercluster';

    import 'leaflet/dist/leaflet.css';
    import 'vue-leaflet-markercluster/dist/style.css';

    import { ref, computed, watch, inject } from 'vue';

    import { refreshPoints, openPoint, fetchPoint, cancelMapVisits } from '@/Helpers/useMap.js';
    import useLocate from '@/Helpers/useLocate.js';

    import Sidebar from '@/Components/Map/Sidebar.vue';
    import Search from '@/Components/Map/Search/Search.vue';

    const props = defineProps({
        context: {
            type: String,
        },
        point: {
            type: Object,
            default: null,
        },
        points: {
            type: Array,
            default: () => [],
        },
        icons: {
            type: Object,
            default: () => ({}),
        },
        mapOptions: {
            type: Object,
            validator: (options) => ['bounds', 'center', 'zoom'].every((el) => options.hasOwnProperty(el)),
        },
        center: {
            type: Array,
        },
        zoom: {
            type: Number,
        },
    });

    const maxBounds = inject('max_map_bounds');

    const map = ref(null);
    const bounds = ref(null);
    const center = ref(null);
    const locationMarker = ref(null);

    const points = computed(() =>
        props.points.map((point) => ({
            ...point,
            current: point.id === props.point?.id,
        }))
    );

    const locate = (value) => {
        center.value = value.center;
        bounds.value = value.bounds;
    };

    const selectPoint = (pointId) => {
        if (['filter', 'search'].includes(props.context)) {
            return fetchPoint(map.value.leafletObject, pointId);
        }

        openPoint(map.value.leafletObject, pointId);
    };

    const sidebarOpen = ref(false);

    const moveend = (event) => refreshPoints(event.target);

    watch(center, (center) => {
        if (locationMarker.value) {
            map.value.leafletObject.removeLayer(locationMarker.value);
        }

        if (!center) {
            return;
        }

        locationMarker.value = L.marker(center).addTo(map.value.leafletObject);
    });

    watch(bounds, (value) => {
        if (!value) {
            return;
        }

        map.value.leafletObject.flyToBounds(
            L.latLngBounds(L.latLng(value[0], value[2]), L.latLng(value[1], value[3])),
            { animate: false }
        );
    });

    const { locateControl } = useLocate();

    const ready = (leafletObject) =>
        locateControl({
            onLocationError: () => refreshPoints(map.value.leafletObject),
        })
            .addTo(leafletObject)
            .start();

    const getMapPinIcon = (point, size) => props.icons[point.service][size];

    const iconCreateFunction = (cluster) =>
        new L.Icon({
            iconUrl: props.icons.markercluster,

            iconSize: [32, 32], // size of the icon
            iconAnchor: [16, 16], // point of the icon which will correspond to marker's location
        });
</script>
