<template>
    <div class="">
        <div class="flex items-center justify-between">
            <h3 id="modal-title" class="text-2xl pl-5 py-3">{{ CONSTANTS.LABELS.ADD_POINT.TITLE }}</h3>
            <button v-on:click="closeModal();">
                <desktop-filter-close-icon></desktop-filter-close-icon>
            </button>
        </div>
        <div class="flex items-center justify-between">
            <span class="pl-5 text-sm">{{ CONSTANTS.LABELS.ADD_POINT.FIRST_STEP.SUBTITLE }}</span>
        </div>

        <div class="mt-3 sm:mt-5 w-full px-5" v-if="Object.keys(nomenclatures).length">
            <div class="space-y-1 mb-3">
                <Listbox
                    as="div"
                    v-model="selectedService"
                >
                    <ListboxLabel class="block text-sm font-medium leading-6 text-gray-900">{{ CONSTANTS.LABELS.SIDEBAR.SERVICE_TYPE_LABEL }}</ListboxLabel>
                    <div class="relative mt-2">
                        <ListboxButton class="relative w-full cursor-default rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <span class="block truncate">{{ selectedService.display_name ? selectedService.display_name : CONSTANTS.LABELS.ADD_POINT.FIRST_STEP.SERVICE_TYPE_PLACEHOLDER }}</span>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            </span>
                        </ListboxButton>

                        <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <ListboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                <ListboxOption
                                    as="template"
                                    v-for="service in nomenclatures.services"
                                    :key="service.id"
                                    :value="service"
                                    v-slot="{ active, selected }"
                                >
                                    <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                        <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{ service.display_name }}</span>

                                        <span v-if="selected" :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                            <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                        </span>
                                    </li>
                                </ListboxOption>
                            </ListboxOptions>
                        </transition>
                    </div>
                </Listbox>

                <template v-if="getError('service_id')">
                    <div class="rounded-md bg-red-50 p-4 mb-2">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">{{ CONSTANTS.LABELS.ADD_POINT.FIRST_STEP.SERVICE_TYPE_REQUIRED }}</h3>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="space-y-1 mb-3">
                <label
                    class="block text-sm font-medium leading-6 text-gray-900 required"
                    for="address"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.FIRST_STEP.EXACT_ADDRESS_LABEL }}
                </label>

                <input
                    style="width: 68%; display: inline"
                    id="address"
                    v-model="stepRequestBody.field_types.address"
                    @keyup.enter="getLatLonOfAddress"
                    :placeholder="CONSTANTS.LABELS.ADD_POINT.FIRST_STEP.EXACT_ADDRESS_PLACEHOLDER"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    name="email"
                    required
                    type="email"
                />

                <button
                    v-on:click="getLatLonOfAddress()"
                    style="width: 30%; margin-left: 2%"
                    type="button"
                    class="bg-indigo-200 -ml-px inline-flex rounded-md bg-white px-3 py-2 text-sm font-semibold  ring-1 ring-inset ring-gray-300 hover:bg-indigo-100 focus:z-10"
                >
                    <span class="text-gray-800">{{ CONSTANTS.LABELS.ADD_POINT.FIRST_STEP.PLACE_PIN }}</span>
                </button>

                <template v-if="getError('address')">
                    <div class="rounded-md bg-red-50 p-4 mb-2">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">{{ CONSTANTS.LABELS.ADD_POINT.FIRST_STEP.ADDRESS_REQUIRED }}</h3>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div
                :class="{'inactiveMap': !Object.keys(point).length}"
                class="space-y-1 mb-3 map h-screen w-full bg-green-900"
                style="height: 350px;"
                v-if="loadMap"
            >
                <l-map
                    :options="mapOptions"
                    ref="map"
                    :center="[latitude, longitude]"
                    :zoom="zoom"
                    @ready="init"
                    v-on:click="addMarker"
                    :use-global-leaflet="false"
                >
                    <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"></l-tile-layer>
                    <l-control
                        position="bottomleft"
                    >
                        <p @click="">
                            <button
                                :disabled="!stepRequestBody.field_types.address.length"
                                id="login_button"
                                class="rounded bg-white px-2 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                type="button"
                                v-on:click="activateMap()"
                            >
                                {{ CONSTANTS.LABELS.ADD_POINT.FIRST_STEP.ADJUST_POINT_ON_MAP }}
                            </button>
                        </p>
                    </l-control>
                    <l-marker
                        v-if="Object.keys(point).length"
                        :lat-lng="[point.lat, point.lon]"
                        @click="markerClicked(point.id)"
                    >
                        <l-icon
                            :icon-size="dynamicSize"
                            icon-url="/assets/images/pin_selected.png" >
                        </l-icon>
                    </l-marker>

                </l-map>
            </div>
            <template v-if="getError('point') && loadMap">
                <div class="rounded-md bg-red-50 p-4 mb-2">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ CONSTANTS.LABELS.ADD_POINT.FIRST_STEP.POINT_REQUIRED }}</h3>
                        </div>
                    </div>
                </div>
            </template>

            <div class="py-2 mb-1" style="text-align: end">
                <button
                    v-on:click="closeModal()"
                    type="button"
                    class="mr-3 rounded bg-white px-2 py-1 text-sm font-semibold text-gray-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.CANCEL }}
                </button>
                <button
                    v-on:click="nextStep()"
                    type="button"
                    class="rounded bg-white px-2 py-1 text-sm font-semibold text-gray-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.NEXT_STEP }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import _ from 'lodash';
import {CONSTANTS} from "@/constants";
import axios, {HttpStatusCode} from "axios";
import DesktopFilterCloseIcon from "../../svg-icons/desktopFilterCloseIcon.vue";
import {XCircleIcon} from '@heroicons/vue/20/solid';
import {CheckIcon, ChevronUpDownIcon} from '@heroicons/vue/20/solid'
import {LMap, LTileLayer, LControlLayers, LMarker, LIcon, LControl } from "@vue-leaflet/vue-leaflet";
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'

export default {
    components: {
        DesktopFilterCloseIcon,
        XCircleIcon,
        CheckIcon,
        ChevronUpDownIcon,
        LIcon,
        LMarker,
        LMap,
        LTileLayer,
        LControlLayers,
        LControl,
        Listbox,
        ListboxButton,
        ListboxLabel,
        ListboxOption,
        ListboxOptions
    },
    props: {
        nomenclatures: {
            type: Object,
            required: true,
        },
    },
    computed: {
        CONSTANTS() {
            return CONSTANTS;
        },
        dynamicSize () {
            return [44, 44 * 1.15];
        },
        mapOptions() {
            if (!this.mapIsActive) {
                return {
                    dragging: false,
                    doubleClickZoom: 0,
                    scrollWheelZoom: 0,
                    zoomControl: false
                }
            }
            return {}
        },
    },
    data() {
        return {
            selectedService: {},
            latitude: CONSTANTS.DEFAULT_LOCATION.LATITUDE,
            longitude: CONSTANTS.DEFAULT_LOCATION.LONGITUDE,
            zoom: 13,
            point: {},
            errors: {},
            mapIsActive: false,
            loadMap: true,
            stepRequestBody: {
                service_id: null,
                field_types: {
                    address: '',
                },
                lat: null,
                lng: null,
            },
        };
    },
    mounted() {
        this.requestCurrentLocation();
    },
    methods: {
        closeModal() {
            this.$emit('close');
        },
        getError(key) {
            return _.get(this, ['errors', key], false);
        },
        requestCurrentLocation()
        {
            const success = (position) =>
            {
                const latitude  = position.coords.latitude;
                const longitude = position.coords.longitude;

                this.latitude = latitude;
                this.longitude = longitude;

                this.hasApprovedLocation = true;
            };

            const error = (err) =>
            {
                console.log(error)
            };
            navigator.geolocation.getCurrentPosition(success, error);
        },
        activateMap() {
            this.mapIsActive = true;
            this.loadMap = false;

            setTimeout(() => {
                this.loadMap = true;
            }, 10);
        },
        addMarker(marker) {
            if (!this.mapIsActive) {
                return;
            }
            this.point = {
                lat: marker.latlng.lat,
                lon: marker.latlng.lng
            };

            this.stepRequestBody.lat = this.point.lat;
            this.stepRequestBody.lng = this.point.lon;

            let url = CONSTANTS.NOMINATIM_URL_DETAILS;
            url = _.replace(url, '{lat}', this.point.lat);
            url = _.replace(url, '{lon}', this.point.lon);

            axios
                .get(
                    url,
                )
                .then((response) => {
                    this.stepRequestBody.field_types.address = response.data.display_name
                })
                .catch((err) => {});
        },
        getLatLonOfAddress() {
            this.point = {};
            this.stepRequestBody.lat = null;
            this.stepRequestBody.lng = null;

            this.latitude = null;
            this.longitude = null;

            let url = CONSTANTS.NOMINATIM_URL_POINTS;
            url = _.replace(url, '{search}', this.stepRequestBody.field_types.address);

            axios
                .get(
                    url,
                )
                .then((response) => {
                    if (!_.get(response, 'data.0.lat', false)) {
                        alert(CONSTANTS.LABELS.ADD_POINT.FIRST_STEP.ADDRESS_NOT_FOUND)
                        return;
                    }

                    this.point = {
                        lat: _.get(response, 'data.0.lat', 0),
                        lon: _.get(response, 'data.0.lon', 0),
                    }

                    //remove the below line to not change the address typed by user
                    this.stepRequestBody.field_types.address = _.get(response, 'data.0.display_name', this.stepRequestBody.field_types.address);

                    this.stepRequestBody.lat = this.point.lat;
                    this.stepRequestBody.lng = this.point.lon;

                    this.latitude = this.point.lat;
                    this.longitude = this.point.lon;
                })
                .catch((err) => {});
        },
        validate() {
            this.errors = {};

            if (!_.get(this, 'stepRequestBody.service_id', false)) {
                this.errors.service_id = true;
            }
            if (!_.get(this, 'stepRequestBody.field_types.address', '').length) {
                this.errors.address = true;
            }
            if (!_.get(this, 'stepRequestBody.lat', '')
                || !_.get(this, 'stepRequestBody.lng', '')
            ) {
                this.errors.point = true;
            }

            return Object.keys(this.errors).length;
        },
        nextStep() {
            if (this.validate()) {
                return;
            }

            this.$emit('stepFinished', {
                nextStep: 'second',
                body: this.stepRequestBody
            })
        }
    },
    watch: {
        selectedService: {
            handler: function (newVal) {
                this.stepRequestBody.service_id = newVal.id
            },
            deep: true,
            immediate: true
        }
    },
};
</script>
