<template>
	<div class="">
		<div class="flex items-center justify-between">
			<h3 id="modal-title" class="text-2xl pl-5 py-3">{{
					CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_OTHER_STEP.TITLE
				}}</h3>
			<button v-on:click="closeModal();">
				<desktop-filter-close-icon></desktop-filter-close-icon>
			</button>
		</div>
		<div class="flex items-center justify-between">
			<span class="py-10 text-sm px-5">{{ CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_OTHER_STEP.SUBTITLE }}</span>
		</div>

		<div v-if="Object.keys(nomenclatures).length" class="mt-3 sm:mt-5 w-full px-5">
			<div class="py-10 mb-3">

                <textarea
					id="description"
					v-model="stepRequestBody.description"
					class="block w-full rounded-md h-[300px] border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
					name="description"
					required
				>
				</textarea>

			</div>

			<template v-if="getError('description')">
				<div class="rounded-md bg-red-50 p-4 mb-2">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800">
								{{ CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_OTHER_STEP.DESCRIPTION_NOT_FOUND }}</h3>
						</div>
					</div>
				</div>
			</template>
			<div class="py-2 mb-1 grid grid-cols-2 text-end align-bottom">
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
					{{ CONSTANTS.LABELS.REPORT_PROBLEM.FINISH_STEP }}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
import _ from 'lodash';
import {CONSTANTS} from "@/constants";
import DesktopFilterCloseIcon from "../../svg-icons/desktopFilterCloseIcon.vue";
import {XCircleIcon} from '@heroicons/vue/20/solid';
import {CheckIcon, ChevronUpDownIcon} from '@heroicons/vue/20/solid';

export default {
	components: {
		DesktopFilterCloseIcon,
		XCircleIcon,
		CheckIcon,
		ChevronUpDownIcon
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
		}
	},
	data ()
	{
		return {
			errors: {},
			stepRequestBody: {
				description: ''
			}
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

			if (!_.get(this, 'stepRequestBody.description', '').length)
			{
				this.errors.description = true;
			}

			return Object.keys(this.errors).length;
		},
		nextStep ()
		{
			if (this.validate())
			{
				return;
			}
			this.$emit('stepFinished', {
				nextStep: 'material-step',
				body: this.stepRequestBody,
				stepCompleted: 'material-option-other'
			});
		}
	}
};
</script>
