<template>
	<div class="flex items-center justify-between">
		<h3 id="modal-title" class="text-2xl pl-5 py-3"></h3>
		<button v-on:click="closeModal();">
			<desktop-filter-close-icon></desktop-filter-close-icon>
		</button>
	</div>
	<div class="h-screen flex items-center justify-center flex-col">
		<h3 class="text-2xl pl-5 py-3 text-black font-medium">
			{{CONSTANTS.LABELS.REPORT_PROBLEM.ADDRESS_STEP.SUCCESS.TITLE}}
		</h3>
		<p class="mt-3 text-center text-sm font-medium text-gray-700">
			{{CONSTANTS.LABELS.REPORT_PROBLEM.ADDRESS_STEP.SUCCESS.SUB_TITLE}}
		</p>
		<p class="mt-3">
			<success-high-five-icon></success-high-five-icon>
		</p>
	</div>
	<div class="flex items-end justify-center">
		<button
			class="rounded bg-black px-2 py-1 w-56 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 border border-black h-10"
			type="button"
			v-on:click="closeModal()"
		>
			OK
		</button>
	</div>
</template>

<script>
import _ from 'lodash';
import {CONSTANTS} from "@/constants";
import axios, {HttpStatusCode} from "axios";
import DesktopFilterCloseIcon from "../../svg-icons/desktopFilterCloseIcon.vue";
import {XCircleIcon} from '@heroicons/vue/20/solid';
import {CheckIcon, ChevronUpDownIcon} from '@heroicons/vue/20/solid';
import {LMap, LTileLayer, LControlLayers, LMarker, LIcon, LControl} from "@vue-leaflet/vue-leaflet";
import {Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions} from '@headlessui/vue';
import SuccessHighFiveIcon from "../../svg-icons/successHighFiveIcon.vue";

export default {
	components: {
		SuccessHighFiveIcon,
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
		mapPoint: {
			type: Object,
			required: true,
		},
		previousStepBody: {
			type: Object,
			required: true,
		}
	},
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS;
		},
		dynamicSize ()
		{
			return [44, 44 * 1.15];
		},
		mapOptions ()
		{
			if (!this.mapIsActive)
			{
				return {
					dragging: false,
					doubleClickZoom: 0,
					scrollWheelZoom: 0,
					zoomControl: false
				};
			}
			return {};
		},
	},
	data ()
	{
		return {
			saved: false
		};
	},
	mounted ()
	{
	},
	methods: {
		closeModal ()
		{
			this.$emit('close');
		},
		getError (key)
		{
			return _.get(this, ['errors', key], false);
		},
		saveProblem()
		{
			if (!this.saved)
			{
				let url = _.replace(CONSTANTS.ROUTES.MAP.POINTS.REPORT, '{id}', this.mapPoint.id);
				axios
					.post(
						CONSTANTS.API_DOMAIN + url,
						{
							lat: this.previousStepBody.lat,
							lng: this.previousStepBody.lng,
							address: this.previousStepBody.address,
							reported_point_issue_type_id: this.previousStepBody.reported_point_issue_type_id,
						}
					)
					.then((response) => {
						if (_.get(response, 'status', 0) === HttpStatusCode.Ok) {
							this.saved = true;
						}
					})
					.catch((err) => {});
			}

		}
	},
	watch: {
		previousStepBody: {
			handler: function (newVal)
			{
				this.saveProblem();
			},
			deep: true,
			immediate: true
		}
	},
};
</script>
