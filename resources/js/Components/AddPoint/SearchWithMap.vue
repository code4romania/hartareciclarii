<template>

    <div class="grid gap-4">
        <label for="search-field" class="block text-sm font-semibold leading-6 text-gray-900"
               v-text="$t('add_point.first_step.exact_address_label')"/>
        <div
            class=" relative flex items-center self-stretch flex-1 gap-2 overflow-hidden bg-white border-b border-gray-200 rounded-full shadow shrink-0 px-3.5"
        >
            <MagnifyingGlassIcon class="w-5 h-full text-gray-400 pointer-events-none shrink-0" aria-hidden="true"/>

            <input
                id="search-field"
                class="block flex-1 h-full py-2.5 px-0 text-gray-900 border-0 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                placeholder="Search..."
                v-model="query"
                :placeholder="$t('add_point.first_step.exact_address_placeholder')"
                type="search"
                name="search"
            />
        </div>

        <ol class="w-full pt-10 -mt-10 overflow-hidden bg-white border border-gray-300 shadow rounded-2xl"
            v-if="searchResults.length>0">

            <li v-for="item in searchResults" :key="item">
                <button type="button"
                        @click="setPoint(item)"
                        class="flex w-full gap-2 px-4 py-2 text-sm hover:bg-gray-100 justify-items-start"
                >
                    <MapPinIcon class="w-5 h-5 fill-gray-400"/>
                    <span class="flex-1 truncate" v-text="item.display_name"/>
                </button>
            </li>
        </ol>

        <div class="flex  justify-end ">
            <button type="button" class="text-primary-500" v-text="$t('add_point.first_step.use_my_current_location')"
                    @click="getCurrentLocation"/>
        </div>
        <div class="min-h-56  h-full">
            <div
                class="absolute  max-w-sm  bg-opacity-70 bg-gray-50  border-gray-100 rounded-lg  dark:bg-gray-800 dark:border-gray-800 dark:hover:bg-gray-700 h-1/3 w-full z-10"
                v-if="loader">
                <div role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                         viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"/>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"/>
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <LMap
                ref="map_pop_up"
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

                class="w-full h-full z-0"
            >
                <LControlZoom position="bottomright"/>
                <LTileLayer
                    url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png"
                    layer-type="base"
                    subdomains="abcd"
                    name="OpenStreetMap"
                    attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                />
                <LMarker v-if="point" :lat-lng="{lat:point.lat, lng:point.lng}" draggable
                         @dragend="dragend"/>
            </LMap>

        </div>
    </div>
</template>

<script setup>

import {ref, watch} from "vue";
import {LControlZoom, LMap, LMarker, LTileLayer} from "@vue-leaflet/vue-leaflet";
import {useDebounceFn, useGeolocation} from "@vueuse/core";

import {MagnifyingGlassIcon, MapPinIcon} from "@heroicons/vue/24/solid/index.js";
import axios from "axios";


const query = ref('')

const runQuery = useDebounceFn(
    () => {
        axios.get(`https://nominatim.openstreetmap.org/search?format=json&q=${query.value}&addressdetails=1&limit=5`).then((response) => {
            searchResults.value = response.data
        });
    },
    500,
    {maxWait: 1000}
);

watch(query, runQuery);


const searchResults = ref([]);
const props = defineProps({
    errors: {
        type: String,
        default: null
    }
})

const map_pop_up = ref(null);

const point = ref(null);
const loader = ref(false);
const moveend = (event) => {
    populatePoint(event.target.getCenter().lat, event.target.getCenter().lng, true);
};
const dragend = (event) => {

    populatePoint(event.target.getLatLng().lat, event.target.getLatLng().lng, true);
};

const getCurrentLocation = () => {
    loader.value = true;
    const {coords, locatedAt, error, resume, pause} = useGeolocation({
        enableHighAccuracy: true,
    })
    watch(error, (value) => {
        if (value instanceof GeolocationPositionError) {
            loader.value = false;
            alert('Please enable location services')
        }

    });
    watch(locatedAt, () => {
        pause();

        populatePoint(coords.value.latitude, coords.value.longitude, true)
        map_pop_up.value.leafletObject.flyTo([coords.value.latitude, coords.value.longitude], 17, {
            animate: false,
        });
    });
};

const setPoint = (item) => {
    populatePoint(item.lat, item.lon, false);
    map_pop_up.value.leafletObject.flyTo([item.lat, item.lon], 17, {
        animate: false,
    });
};

const populatePoint = (lat, lng, setDisplayName) => {
    point.value = {
        lat: lat,
        lng: lng
    }
    if (setDisplayName) {
        //TODO maybe move this in backend
        loader.value = true;
        axios.get(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`).then((response) => {
            query.value = response.data.display_name;
            loader.value = false;
        });
    }
};
const ready = (leafletObject) => {
    populatePoint(leafletObject.getCenter().lat, leafletObject.getCenter().lng, false);
};
</script>
