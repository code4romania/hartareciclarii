<template>
    <div class="">
        <div class="flex items-center justify-between">
            <h3 id="modal-title" class="text-2xl pl-5 py-3">{{ CONSTANTS.LABELS.ADD_POINT.TITLE }}</h3>
            <button v-on:click="closeModal();">
                <desktop-filter-close-icon></desktop-filter-close-icon>
            </button>
        </div>
        <div class="flex items-center justify-between">
            <span class="pl-5 text-sm">{{ CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.SUBTITLE }}</span>
        </div>
        <div class="mt-3 sm:mt-5 w-full px-5" v-if="Object.keys(previousStepBody).length">
			<template v-if="Object.keys(validationErrors).length > 0">
				<div class="rounded-md bg-red-50 p-4 mb-2">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800" v-for="error of validationErrors">
								{{ error[0] }}</h3>
						</div>
					</div>
				</div>
			</template>
            <div class="space-y-1 mb-3">
                <p class="text-xl">{{ containerType() }}</p>
            </div>

            <div class="space-y-1 mb-3">
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    {{ CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.ADDRESS }}
                </p>
                <p class="text-gray-600 ml-6">
                    {{ previousStepBody.field_types.address }}
                </p>
            </div>

            <div
                class="space-y-1 mb-3 map h-screen w-full bg-green-900 inactiveMap"
                style="height: 250px;"
                v-if="Object.keys(previousStepBody).length && activeStep === 'third'"
            >
                <l-map
                    :options="{
                        dragging: false,
                        doubleClickZoom: 0,
                        scrollWheelZoom: 0,
                        zoomControl: false
                    }"
                    ref="map"
                    :center="[previousStepBody.lat, previousStepBody.lng]"
                    :zoom="15"
                >
                    <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"></l-tile-layer>
                    <l-marker
                        :lat-lng="[parseFloat(previousStepBody.lat), parseFloat(previousStepBody.lng)]"
                    >
                        <l-icon
                            :icon-size="dynamicSize"
                            icon-url="/assets/images/pin_selected.png" >
                        </l-icon>
                    </l-marker>

                </l-map>
            </div>

            <div
                v-if="hasProgram()"
                class="space-y-1 mb-3 mt-5"
            >
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                    </svg>

                    {{ CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.PROGRAM }}
                </p>
                <p class="text-gray-600 ml-5">
                    {{ previousStepBody.opening_hours.startDay }}-{{ previousStepBody.opening_hours.endDay }}, {{ previousStepBody.opening_hours.startHour }}-{{ previousStepBody.opening_hours.endHour }}
                </p>
            </div>

            <div class="space-y-1 mb-3 mt-5" v-if="previousStepBody.material_recycling_point">
                <p>
                    {{ CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.COLLECTED_MATERIALS }}
                </p>
                <p class="text-gray-600 ml-5">
                    <span v-for="material of nomenclatures.material_recycling_points">
                        <span v-if="previousStepBody.material_recycling_point.includes(material.id)" class="block">
                            {{ material.name }}
                        </span>
                    </span>
                </p>
            </div>

            <div
                v-if="previousStepBody.field_types.notes"
                class="space-y-1 mb-3 mt-5"
            >
                <p>
                    {{ CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.OBSERVATIONS }}
                </p>
                <p class="text-gray-600 ml-5">
                    {{ previousStepBody.field_types.notes }}
                </p>
            </div>

            <div class="space-y-1 mb-3 mt-5 flex-row">
                <p class="text-gray-600 inline">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    {{ previousStepBody.field_types.offers_transport ? CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.OFFERS_SHIP : CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.DOESNT_OFFERS_SHIP }}
                </p>

                <p class="text-gray-600 inline ml-5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 7.756a4.5 4.5 0 100 8.488M7.5 10.5h5.25m-5.25 3h5.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                    {{ previousStepBody.field_types.offers_money ? CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.OFFERS_MONEY : CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.DOESNT_OFFERS_MONEY }}
                </p>
            </div>

            <div
                v-if="previousStepBody.field_types.website"
                class="space-y-1 mb-3 mt-5"
            >
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                    </svg>
                    {{ CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.WEBSITE }}
                </p>
                <p class="text-gray-600 ml-5">
                    {{ previousStepBody.field_types.website }}
                </p>
            </div>

            <div
                v-if="previousStepBody.field_types.phone_no"
                class="space-y-1 mb-3 mt-5"
            >
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    {{ CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.PHONE }}
                </p>
                <p class="text-gray-600 ml-5">
                    {{ previousStepBody.field_types.phone_no }}
                </p>
            </div>

            <div
                v-if="previousStepBody.field_types.email"
                class="space-y-1 mb-3 mt-5"
            >
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    {{ CONSTANTS.LABELS.ADD_POINT.THIRD_STEP.EMAIL }}
                </p>
                <p class="text-gray-600 ml-5">
                    {{ previousStepBody.field_types.email }}
                </p>
            </div>

            <div class="py-2 mb-1" style="text-align: end">
                <button
                    v-on:click="$emit('backToStep', 'second')"
                    type="button"
                    class="mr-3 rounded bg-white px-2 py-1 text-sm font-semibold text-gray-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.BACK }}
                </button>
                <button
                    v-on:click="nextStep()"
                    type="button"
                    class="rounded bg-white px-2 py-1 text-sm font-semibold text-gray-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                >
                    {{ CONSTANTS.LABELS.ADD_POINT.FINISH_STEPS }}
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
import eventBus from "../../../eventBus.js";
import 'vue3-treeselect/dist/vue3-treeselect.css'
import {LMap, LTileLayer, LControlLayers, LMarker, LIcon, LControl } from "@vue-leaflet/vue-leaflet";


export default {
    components: {
        DesktopFilterCloseIcon,
        XCircleIcon,
        LIcon,
        LMarker,
        LMap,
        LTileLayer,
        LControlLayers,
        LControl
    },
    props: {
        nomenclatures: {
            type: Object,
            required: true,
        },
        activeStep: {
            type: String,
            required: true,
        },
        previousStepBody: {
            type: Object,
            required: true,
        },
		validationErrors: {
			type: Object,
			required: false,
			default: {}
		},
    },
    computed: {
        CONSTANTS() {
            return CONSTANTS;
        },
        dynamicSize () {
            return [48, 64];
        },
    },
    data() {
        return {
            /*previousStepBody: {
                "service_id": 1,
                "field_types": {
                    "address": "Strada Halaicului, Mogoșoaia, Ilfov, 077135, Romania",
                    "managed_by": "adminstrator smecher",
                    "website": "website url",
                    "email": "email@test",
                    "phone_no": "930123131",
                    "offers_money": 1,
                    "offers_transport": 0,
                    "notes": "asdadsad"
                },
                "lat": "44.5332029",
                "lng": "25.9768303",
                "point_type_id": 1,
                "material_recycling_point": [1, 13, 14],
                "opening_hours": {"startDay": "Luni", "endDay": "Vineri", "startHour": "06:00", "endHour": "10:00"}
            }*/
			errors: {},
        };
    },
    mounted() {},
    methods: {
        closeModal() {
            this.$emit('close');
        },
        containerType() {
            for (const service of _.get(this, 'nomenclatures.services', [])) {
                for (const point of service.point_types) {
                    if (point.id === this.previousStepBody.service_id) {
                        return point.display_name;
                    }
                }
            }
        },
        hasProgram() {
            if (_.get(this, 'previousStepBody.opening_hours.startDay', false)
                && _.get(this, 'previousStepBody.opening_hours.startHour', false)
                && _.get(this, 'previousStepBody.opening_hours.endDay', false)
                && _.get(this, 'previousStepBody.opening_hours.endHour', false)
            ) {
                return true;
            }

            return false;
        },
        nextStep() {
            this.$emit('stepFinished', {
                nextStep: 'finish',
            })
        }
    },
    watch: {
        nomenclatures: {
            handler: function (newVal) {

            },
            deep: true,
            immediate: true
        }
    },
};
</script>
