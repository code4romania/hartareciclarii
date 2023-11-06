<template>
	<div class="flex items-center justify-between">
		<h3 id="modal-title" class="text-2xl pl-5 py-3">{{ CONSTANTS.LABELS.REPORT_PROBLEM.TITLE }}</h3>
		<button v-on:click="closeModal();">
			<desktop-filter-close-icon></desktop-filter-close-icon>
		</button>
	</div>
	<div class="flex items-center justify-between">
		<span class="pl-5 text-sm">{{ CONSTANTS.LABELS.REPORT_PROBLEM.FIRST_STEP.SUBTITLE }}</span>
	</div>

	<div v-if="Object.keys(nomenclatures).length" class="mt-3 sm:mt-5 w-full h-fit px-5">
		<div class="space-y-1 mb-3">
			success finish step
			{{previousStepBody}}
		</div>
		<div class="py-2 mb-1 w-full mt-2 text-center" >

			<button
				class="rounded bg-black px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 border border-black h-10 w-56"
				type="button"
				v-on:click="closeModal()"
			>
				OK
			</button>
		</div>
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
	},
	watch: {
		previousStepBody: {
			handler: function (newVal)
			{
				console.log(this.previousStepBody);
			},
			deep: true,
			immediate: true
		}
	},
};
</script>
