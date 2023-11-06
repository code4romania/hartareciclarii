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
			<fieldset class="mt-4">
				<div class="space-y-4">
					<div v-for="nomenclature in nomenclatures.reported_point_issue_types" class="flex items-center">
						<input
							:id="nomenclature.id"
							:value="nomenclature.id"
							name="reported_point_issue_type_id"
							type="radio"
							class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600"
							v-model="stepRequestBody.reported_point_issue_type_id"
						/>
						<label :for="nomenclature.id" class="ml-3 block text-sm font-medium leading-6 text-gray-900">{{ nomenclature.title }}</label>
					</div>
				</div>
			</fieldset>

			<template v-if="getError('reported_point_issue_type_id')">
				<div class="rounded-md bg-red-50 p-4 mb-2">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800">
								{{ CONSTANTS.LABELS.REPORT_PROBLEM.FIRST_STEP.ISSUE_TYPE_REQUIRED }}</h3>
						</div>
					</div>
				</div>
			</template>
		</div>

		<div class="py-2 mb-1 grid grid-cols-2 text-end align-bottom" >
			<button
				class="rounded bg-white px-2 py-1 text-sm font-semibold text-gray-600 shadow-sm hover:bg-gray-200 border border-black h-10 mr-3"
				type="button"
				v-on:click="closeModal()"
			>
				{{ CONSTANTS.LABELS.REPORT_PROBLEM.CANCEL }}
			</button>
			<button
				class="rounded bg-black px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 border border-black h-10"
				type="button"
				v-on:click="nextStep()"
			>
				{{ CONSTANTS.LABELS.REPORT_PROBLEM.NEXT_STEP }}
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
			selectedService: {},
			errors: {},
			stepRequestBody:
				{
					reported_point_issue_type_id: null,
				},
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
		validate ()
		{
			this.errors = {};

			if (!_.get(this, 'stepRequestBody.reported_point_issue_type_id', false))
			{
				this.errors.reported_point_issue_type_id = true;
			}

			return Object.keys(this.errors).length;
		},
		nextStep ()
		{
			if (this.validate())
			{
				return;
			}
			let nextStep = 'first';
			for (const nomenclature of this.$props.nomenclatures.reported_point_issue_types)
			{
				if (nomenclature.id == this.stepRequestBody.reported_point_issue_type_id)
				{
					nextStep = nomenclature.steps[0];
				}

			}
			this.$emit('stepFinished',
		{
				nextStep: nextStep,
				body: this.stepRequestBody
			});
		}
	},
	watch: {
		selectedService: {
			handler: function (newVal)
			{
				this.stepRequestBody.reported_point_issue_type_id = newVal.id;
			},
			deep: true,
			immediate: true
		}
	},
};
</script>
