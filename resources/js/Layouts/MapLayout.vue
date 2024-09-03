<template>
    <div class="flex h-full">
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative lg:hidden" @close="sidebarOpen = false">
                <TransitionChild
                    as="template"
                    enter="transition-opacity ease-linear duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="transition-opacity ease-linear duration-300"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-gray-900/80" />
                </TransitionChild>

                <div class="fixed inset-0 flex">
                    <TransitionChild
                        as="template"
                        enter="transition ease-in-out duration-300 transform"
                        enter-from="-translate-x-full"
                        enter-to="translate-x-0"
                        leave="transition ease-in-out duration-300 transform"
                        leave-from="translate-x-0"
                        leave-to="-translate-x-full"
                    >
                        <DialogPanel class="relative flex flex-1 w-full max-w-xs mr-16">
                            <TransitionChild
                                as="template"
                                enter="ease-in-out duration-300"
                                enter-from="opacity-0"
                                enter-to="opacity-100"
                                leave="ease-in-out duration-300"
                                leave-from="opacity-100"
                                leave-to="opacity-0"
                            >
                                <div class="absolute top-0 flex justify-center w-16 pt-5 left-full">
                                    <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                                        <span class="sr-only">Close sidebar</span>
                                        <XMarkIcon class="w-6 h-6 text-white" aria-hidden="true" />
                                    </button>
                                </div>
                            </TransitionChild>

                            <Sidebar class="flex" @select-point="selectPoint" />
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Static sidebar for desktop -->
        <Sidebar class="hidden border-r border-gray-200 lg:flex" @select-point="selectPoint" />

        <div class="relative flex-1">
            <div
                class="absolute z-10 flex flex-col gap-4 overflow-hidden pointer-events-none inset-3 lg:left-6 lg:top-4 lg:right-auto sm:w-80 md:w-96"
            >
                <Search class="z-20 pointer-events-auto" :map="map" @locate="locate" />

                <slot :map="map" />
            </div>

            <LMap
                ref="map"
                :useGlobalLeaflet="true"
                :min-zoom="8"
                :max-zoom="18"
                :center="mapOptions.center"
                :zoom="mapOptions.zoom"
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

    import DefaultLayout from '@/Layouts/DefaultLayout.vue';

    import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
    import { XMarkIcon } from '@heroicons/vue/24/outline';

    import { LMap, LControl, LControlScale, LControlZoom, LIcon, LMarker, LTileLayer } from '@vue-leaflet/vue-leaflet';
    import { LMarkerClusterGroup } from 'vue-leaflet-markercluster';

    import 'leaflet/dist/leaflet.css';
    import 'vue-leaflet-markercluster/dist/style.css';

    import { ref, computed, watch } from 'vue';
    import { router } from '@inertiajs/vue3';

    import route from '@/Helpers/useRoute.js';
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
        console.log(value);
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

        map.value.leafletObject.flyToBounds(L.latLngBounds(L.latLng(value[0], value[2]), L.latLng(value[1], value[3])), {
            animate: false,
        });
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
