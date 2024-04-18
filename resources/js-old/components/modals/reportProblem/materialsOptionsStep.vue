<template>
	<div class="">
		<div class="flex items-center justify-between">
			<h3 id="modal-title" class="text-2xl pl-5 py-3">{{
					CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_OPTIONS_STEP.TITLE
				}}</h3>
			<button v-on:click="closeModal();">
				<desktop-filter-close-icon></desktop-filter-close-icon>
			</button>
		</div>
		<div
			class="flex items-center justify-between flex-col"
			v-if="items.length > 0"
		>
			<span class="pt-10 pb-3 text-sm px-5 w-full">{{ CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_OPTIONS_STEP.SUBTITLE }}</span>
			<fieldset>
				<legend class="sr-only"></legend>
				<div class="space-y-2 px-5">
					<div
						class="relative flex items-start"
						v-for="item in items"
					>
						<div
							class="flex h-6 items-center"
						>
							<input
								:id="`material_issue_${item.id}`"
								:name="`material_issue[${item.id}]`"
								type="checkbox"
								class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
								v-model="stepRequestBody.material_issue"
								:value="item.id"
							/>
						</div>
						<div class="ml-3">
							<label :for="`material_issue_${item.id}`" class="text-gray-700 text-sm font-normal">{{item.title}}</label>
						</div>
					</div>
				</div>
			</fieldset>
			<template v-if="getError('material_issue')">
				<div class="rounded-md bg-red-50 p-4 mb-2 mt-5 w-full">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800">
								{{ CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_OPTIONS_STEP.OPTIONS_NOT_FOUND }}</h3>
						</div>
					</div>
				</div>
			</template>
		</div>


		<div v-if="Object.keys(nomenclatures).length" class="mt-3 sm:mt-5 w-full px-5">
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
					{{ CONSTANTS.LABELS.REPORT_PROBLEM.NEXT_STEP }}
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
		},
		items()
		{
			let issueTypeId = this.previousStepBody.reported_point_issue_type_id;
			for (const item of _.get(this, 'nomenclatures.reported_point_issue_types', []))
			{

				if (item.id == issueTypeId)
				{
					if (item.items.length > 0)
					{
						return item.items;
					}
				}
			}

			return [];
		}
	},
	data ()
	{
		return {
			errors: {},
			stepRequestBody: {
				material_issue: []
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

			if (_.get(this, 'stepRequestBody.material_issue', []).length < 1)
			{
				this.errors.material_issue = true;
			}

			return Object.keys(this.errors).length;
		},
		nextStep ()
		{
			if (this.validate())
			{
				return;
			}
			this.stepRequestBody.material_issue = this.stepRequestBody.material_issue.sort();
			this.$emit('stepFinished', {
				nextStep: 'material-step',
				body: this.stepRequestBody,
				stepCompleted: 'materials-options'
			});
		}
	}
};
</script>
