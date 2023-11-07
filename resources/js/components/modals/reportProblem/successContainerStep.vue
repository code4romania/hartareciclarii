<template>
	<div class="flex items-center justify-between">
		<h3 id="modal-title" class="text-2xl pl-5 py-3"></h3>
		<button v-on:click="closeModal();">
			<desktop-filter-close-icon></desktop-filter-close-icon>
		</button>
	</div>
	<div class="h-screen flex items-center justify-center flex-col">
		<h3 class="text-2xl pl-5 py-3 text-black font-medium">
			{{CONSTANTS.LABELS.REPORT_PROBLEM.CONTAINER_STEP.SUCCESS.TITLE}}
		</h3>
		<p class="mt-3 text-center text-sm font-medium text-gray-700">
			{{CONSTANTS.LABELS.REPORT_PROBLEM.CONTAINER_STEP.SUCCESS.SUB_TITLE}}
		</p>
		<p class="mt-3">
			<success-high-five-icon></success-high-five-icon>
		</p>

		<p class="mt-3 text-center text-sm font-medium text-gray-700">
			{{CONSTANTS.LABELS.REPORT_PROBLEM.CONTAINER_STEP.SUCCESS.SUB_TITLE_UNDER_IMAGE}}
		</p>

		<div class="bg-neutral-100 w-full mt-5 py-3 px-5 rounded-lg">
			<span class="text-sm font-medium text-black">
				{{pointTitle}}
			</span>
			<span
				class="text-sm font-medium text-black flex items-center gap-x-2 flex-col mt-3"
				v-if="pointWebsite != ''"
			>
				<span class="w-full items-center inline-block mt-3">
					<point-details-website-icon class="inline"></point-details-website-icon>
					{{CONSTANTS.LABELS.REPORT_PROBLEM.CONTAINER_STEP.SUCCESS.WEBSITE}}
				</span>
				<span class="w-full inline-block mt-1 text-black text-xs font-normal">
					{{pointWebsite}}
				</span>
			</span>
			<span
				class="text-sm font-medium text-black flex items-center gap-x-2 flex-col mt-3"
				v-if="pointPhone != ''"
			>
				<span class="w-full items-center inline-block mt-3">
					<point-details-phone-icon class="inline"></point-details-phone-icon>
					{{CONSTANTS.LABELS.REPORT_PROBLEM.CONTAINER_STEP.SUCCESS.PHONE}}
				</span>
				<span class="w-full inline-block mt-1 underline text-black text-xs font-normal">
					{{pointPhone}}
				</span>
			</span>
		</div>
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
import SuccessHighFiveIcon from "../../svg-icons/successHighFiveIcon.vue";
import PointDetailsWebsiteIcon from "../../svg-icons/pointDetailsWebsiteIcon.vue";
import PointDetailsPhoneIcon from "../../svg-icons/pointDetailsPhoneIcon.vue";

export default {
	components: {
		PointDetailsPhoneIcon,
		PointDetailsWebsiteIcon,
		SuccessHighFiveIcon,
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
		pointTitle()
		{
			let service = _.get(this, ['mapPoint', 'service', 'display_name'], '');
			let managedBy = '';

			for (const field of _.get(this, ['mapPoint', 'fields'], {}))
			{
				if (field.field.field_name == 'managed_by')
				{
					managedBy = field.value;
					break;
				}
			}

			return `${service} ${managedBy}`;
		},
		pointWebsite()
		{
			let website = '';

			for (const field of _.get(this, ['mapPoint', 'fields'], {}))
			{
				if (field.field.field_name == 'website')
				{
					website = field.value;
					break;
				}
			}

			return website;
		},
		pointPhone()
		{
			let phone = '';

			for (const field of _.get(this, ['mapPoint', 'fields'], {}))
			{
				if (field.field.field_name == 'phone_no')
				{
					phone = field.value;
					break;
				}
			}

			return phone;
		}
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
							description: this.previousStepBody.description,
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
