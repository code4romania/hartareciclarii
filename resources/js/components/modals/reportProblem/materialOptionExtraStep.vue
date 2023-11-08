<template>
	<div class="">
		<div class="flex items-center justify-between">
			<h3 id="modal-title" class="text-2xl pl-5 py-3">{{
					CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_NOT_COLLECTED_STEP.TITLE
				}}</h3>
			<button v-on:click="closeModal();">
				<desktop-filter-close-icon></desktop-filter-close-icon>
			</button>
		</div>
		<div
			class="flex items-center justify-between flex-col"
			v-if="items.length > 0"
		>
			<span class="pt-10 pb-3 text-sm px-5 w-full" v-html="CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_NOT_COLLECTED_STEP.SUBTITLE"></span>
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
							<div
								class="relative flex items-start"
							>
								<div
									class="flex h-6 items-center"
								>
									<input
										:id="`material_issue_extra_${child.id}`"
										:name="`material_issue_extra_[${child.id}]`"
										type="checkbox"
										class="w-4 h-4 text-red-600 bg-gray-100 rounded focus:ring-red-500"
										v-model="stepRequestBody.material_issue_extra"
										:value="child.id"
									/>
								</div>
								<div class="ml-3">
									<label
										:for="`material_issue_extra_${child.id}`"
										class="text-gray-700 text-sm font-normal"
										:class="{ 'line-through': isChecked(child.id) }"
									>
										{{child.label}}
									</label>
								</div>
							</div>
					</template>
					</div>
				</template>

			</fieldset>
			<template v-if="getError('material_issue_extra')">
				<div class="rounded-md bg-red-50 p-4 mb-2 mt-5 w-full">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800">
								{{ CONSTANTS.LABELS.REPORT_PROBLEM.MATERIALS_NOT_COLLECTED_STEP.MATERIALS_NOT_FOUND }}</h3>
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
			let parents = [];
			for (const filter of _.get(this, ['nomenclatures', 'material_recycling_points'], []))
			{
				if (!filter.parent)
				{
					let filterToAppend =
						{
							id: filter.id,
							label: filter.name,
							icon: filter.icon,
							children: []
						};
					for (const childrenFilter of _.get(this, ['mapPoint', 'materials'], []))
					{
						if (childrenFilter.parent === filter.id)
						{
							filterToAppend.children.push({
								id: childrenFilter.id,
								label: childrenFilter.name,
							});
							parents.push(childrenFilter.parent);
							parents.push(filter.id);
						}
					}
					materials.push(filterToAppend);
				}
			}
			let uniqueParents = parents.filter((x, y) => parents.indexOf(x) == y);
			if (uniqueParents.length > 0)
			{
				let temp = materials;
				let filtered  = temp.filter(function(value, index, array)
				{
					return uniqueParents.includes(value.id);
				});

				materials = filtered;
			}

			return materials;
		}
	},
	data ()
	{
		return {
			errors: {},
			stepRequestBody: {
				material_issue_extra: []
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

			if (_.get(this, 'stepRequestBody.material_issue_extra', []).length < 1)
			{
				this.errors.material_issue_extra = true;
			}

			return Object.keys(this.errors).length;
		},
		isChecked(id)
		{
			if (_.get(this, 'stepRequestBody.material_issue_extra', []).includes(id))
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
				stepCompleted: 'material-option-extra'
			});
		}
	}
};
</script>
