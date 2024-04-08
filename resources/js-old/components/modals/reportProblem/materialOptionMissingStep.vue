<template>
	<div class="">
		<div class="flex items-center justify-between">
			<h3 id="modal-title" class="text-2xl pl-5 py-3">{{
					CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_MISSING_STEP.TITLE
				}}</h3>
			<button v-on:click="closeModal();">
				<desktop-filter-close-icon></desktop-filter-close-icon>
			</button>
		</div>
		<div
			class="flex items-center justify-between flex-col"
			v-if="items.length > 0"
		>
			<span class="pt-10 pb-3 text-sm px-5 w-full" v-html="CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_MISSING_STEP.SUBTITLE"></span>
			<fieldset>
				<legend class="sr-only"></legend>
				<template
					v-for="item in items"

				>
					<div class="space-y-2 px-5">
						{{item.label}}
					</div>
					<div class="space-y-2 px-10">
					<template
						v-for="child in item.children"
					>
							<div class="relative flex items-start">
								<div class="flex h-6 items-center">
									<input
										:id="`material_issue_missing_${child.id}`"
										:name="`material_issue_missing_[${child.id}]`"
										type="checkbox"
										class="w-4 h-4 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
										v-model="material_issue_missing_data"
										:value="child.id"
										:disabled="child.existing"
										:class="{'text-gray-700': child.existing, 'text-blue-500': !child.existing}"
									/>
								</div>
								<div class="ml-3">
									<label
										:for="`material_issue_missing_${child.id}`"
										class="text-gray-700 text-sm font-normal"
									>
										{{child.label}} existing {{child.existing}}
									</label>
								</div>
							</div>
					</template>
					</div>
				</template>

			</fieldset>
			<template v-if="getError('material_issue_missing')">
				<div class="rounded-md bg-red-50 p-4 mb-2 mt-5 w-full">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800">
								{{ CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_MISSING_STEP.MATERIALS_NOT_FOUND }}</h3>
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
			let materials = [];
			this.pointMaterials = [];
			for (const material of _.get(this, ['mapPoint', 'materials'], []))
			{
				this.pointMaterials.push(material.id);
				this.material_issue_missing_data.push(material.id);
			}

			for (const filter of _.get(this, ['nomenclatures', 'material_recycling_points'], [])) {
				if (!filter.parent) {
					let filterToAppend = {
						id: filter.id,
						label: filter.name,
						existing: false,
						children: []
					};
					for (const childrenFilter of _.get(this, ['nomenclatures', 'material_recycling_points'], [])) {
						if (childrenFilter.parent === filter.id)
						{
							let existing = false;
							if (this.pointMaterials.includes(childrenFilter.id))
							{
								existing = true;
							}

							filterToAppend.children.push({
								id: childrenFilter.id,
								label: childrenFilter.name,
								existing: existing
							});
						}
					}
					materials.push(filterToAppend);
				}
			}

			return materials;
		}
	},
	data ()
	{
		return {
			errors: {},
			stepRequestBody: {
				material_issue_missing: []
			},
			material_issue_missing_data: [],
			pointMaterials: []
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

			if (_.get(this, 'stepRequestBody.material_issue_missing', []).length < 1)
			{
				this.errors.material_issue_missing = true;
			}

			return Object.keys(this.errors).length;
		},
		isChecked(id)
		{
			if (_.get(this, 'stepRequestBody.material_issue_missing', []).includes(id))
			{
				return true;
			}

			return false;
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
				stepCompleted: 'material-option-missing'
			});
		}
	},
	watch: {
		material_issue_missing_data: {
			handler: function (newVal)
			{
				let items = newVal.filter((x, y) => newVal.indexOf(x) == y);
				this.stepRequestBody.material_issue_missing = [];
				if (items.length > 0)
				{
					for (const item of items)
					{
						if (!this.pointMaterials.includes(item))
						{
							if (!this.stepRequestBody.material_issue_missing.includes(item))
							{
								this.stepRequestBody.material_issue_missing.push(item);
							}
						}
					}
				}
			},
			deep: true,
			immediate: true
		}
	},
};
</script>
