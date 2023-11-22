<template>
	<TransitionRoot :show="isOpen" as="template">
		<Dialog as="div" class="relative z-50" @close="closeModal();">
			<TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
							 leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
				<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"/>
			</TransitionChild>

			<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
			<div class="fixed inset-0 z-50 w-screen overflow-y-auto">
				<div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
					<div
						id="containerWithScroll"
						class="relative transform flex flex-col rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:w-full sm:max-w-lg sm:p-6 h-[80vh] overflow-auto">
						<first-step
							v-if="activeStep === 'first'"
							:map-point="mapPoint"
							:nomenclatures="nomenclatures"
							@close="closeModal();"
							@stepFinished="stepFinished($event)"
						></first-step>
						<address-step
							v-if="activeStep === 'address'"
							:nomenclatures="nomenclatures"
							:map-point="mapPoint"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="stepFinished($event)"
						></address-step>
						<schedule-step
							v-show="activeStep === 'schedule'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							:map-point="mapPoint"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="stepFinished($event)"
						></schedule-step>
						<container-step
							v-show="activeStep === 'container'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							:map-point="mapPoint"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="stepFinished($event)"
						></container-step>
						<takeover-step
							v-show="activeStep === 'takeover'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							:map-point="mapPoint"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="stepFinished($event)"
						></takeover-step>
						<other-problem-step
							v-show="activeStep === 'other-problem'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							:map-point="mapPoint"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="stepFinished($event)"
						></other-problem-step>
						<materials-options-step
							v-show="activeStep === 'materials-options'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							:map-point="mapPoint"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="stepFinished($event)"
						></materials-options-step>
						<material-option-extra-step
							v-show="activeStep === 'material-option-extra'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							:map-point="mapPoint"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="stepFinished($event)"
						></material-option-extra-step>
						<material-option-missing-step
							v-show="activeStep === 'material-option-missing'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							:map-point="mapPoint"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="stepFinished($event)"
						></material-option-missing-step>
						<material-option-other-step
							v-show="activeStep === 'material-option-other'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							:map-point="mapPoint"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="stepFinished($event)"
						></material-option-other-step>

						<success-address-step
							v-if="activeStep === 'success-address'"
							:active-step="activeStep"
							:map-point="mapPoint"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
						></success-address-step>
						<success-schedule-step
							v-if="activeStep === 'success-schedule'"
							:active-step="activeStep"
							:map-point="mapPoint"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
						></success-schedule-step>
						<success-container-step
							v-if="activeStep === 'success-container'"
							:active-step="activeStep"
							:map-point="mapPoint"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
						></success-container-step>
						<success-other-problem-step
							v-if="activeStep === 'success-other'"
							:active-step="activeStep"
							:map-point="mapPoint"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
						></success-other-problem-step>
						<success-takeover-step
							v-if="activeStep === 'success-takeover'"
							:active-step="activeStep"
							:map-point="mapPoint"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
						></success-takeover-step>
						<success-material-options-step
							v-if="activeStep === 'success-material-options'"
							:active-step="activeStep"
							:map-point="mapPoint"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
						></success-material-options-step>
					</div>
				</div>
			</div>
		</Dialog>
	</TransitionRoot>
</template>

<script>
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue';
import {CheckIcon} from '@heroicons/vue/24/outline';
import {CONSTANTS} from "@/constants";
import DesktopFilterCloseIcon from "../../svg-icons/desktopFilterCloseIcon.vue";
import axios, {HttpStatusCode} from "axios";
import _ from "lodash";
import firstStep from "./firstStep.vue";
import AddressStep from "./addressStep.vue";
import SuccessAddressStep from "./successAddressStep.vue";
import ScheduleStep from "./scheduleStep.vue";
import SuccessScheduleStep from "./successScheduleStep.vue";
import SuccessContainerStep from "./successContainerStep.vue";
import ContainerStep from "./containerStep.vue";
import OtherProblemStep from "./otherProblemStep.vue";
import SuccessOtherProblemStep from "./successOtherProblemStep.vue";
import TakeoverStep from "./takeoverStep.vue";
import SuccessTakeoverStep from "./successTakeoverStep.vue";
import MaterialsOptionsStep from "./materialsOptionsStep.vue";
import MaterialOptionExtraStep from "./materialOptionExtraStep.vue";
import MaterialOptionMissingStep from "./materialOptionMissingStep.vue";
import MaterialOptionOtherStep from "./materialOptionOtherStep.vue";
import SuccessMaterialOptionsStep from "./successMaterialOptionsStep.vue";

export default {
	components: {
		SuccessMaterialOptionsStep,
		MaterialOptionOtherStep,
		MaterialOptionMissingStep,
		MaterialOptionExtraStep,
		MaterialsOptionsStep,
		SuccessTakeoverStep,
		TakeoverStep,
		SuccessOtherProblemStep,
		OtherProblemStep,
		ContainerStep,
		SuccessContainerStep,
		SuccessScheduleStep,
		ScheduleStep,
		SuccessAddressStep,
		AddressStep,
		firstStep,
		DesktopFilterCloseIcon,
		Dialog,
		DialogPanel,
		DialogTitle,
		TransitionChild,
		TransitionRoot,
		CheckIcon
	},
	props: {
		isOpen: {
			type: Boolean,
			required: true,
			default: false
		},
		isAuthenticated: {
			type: Boolean,
			required: true,
			default: false
		},
		userInfo: {
			type: Object,
			required: false
		},
		mapPoint: {
			type: Object,
			required: true
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
			activeStep: 'first',
			nomenclatures: {},
			requestBody: {},
		};
	},
	mounted ()
	{
		if (this.isOpen)
		{
			console.log(`opened report modal`);
		}
		this.getNomenclatureValues();
	},
	methods: {
		closeModal ()
		{
			this.$emit('close');
			this.activeStep = 'first';
		},
		resetModal ()
		{
			this.$emit('reset');
			this.activeStep = 'first';
		},
		getNomenclatureValues ()
		{
			axios
				.get(
					CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.STATIC.NOMENCLATURES.GET,
				)
				.then((response) =>
				{
					this.nomenclatures = _.get(response, 'data', {});
				})
				.catch((err) =>
				{
				});
		},
		backToStep (step)
		{
			const myDiv = document.getElementById('containerWithScroll');
			myDiv.scrollTop = 0;

			this.activeStep = step;
		},
		stepFinished (stepData)
		{
			this.requestBody = {...this.requestBody, ...stepData.body};

			if (stepData.nextStep == 'material-step')
			{
				console.log(`steps data`, this.nomenclatures.reported_point_issue_types, this.requestBody);
				let availableSteps = [];
				for (const item of this.nomenclatures.reported_point_issue_types)
				{
					if (item.id == this.requestBody.reported_point_issue_type_id)
					{
						for (const step of item.items)
						{
							if (this.requestBody.material_issue.includes(step.id))
							{
								availableSteps.push(step.step);
							}
						}
					}
				}

				if (stepData.stepCompleted == 'materials-options')
				{
					this.activeStep = availableSteps[0];
				}
				else
				{
					let index = availableSteps.indexOf(stepData.stepCompleted);
					if (typeof availableSteps[(index + 1)] !== 'undefined')
					{
						this.activeStep = availableSteps[(index + 1)];
					}
					else
					{
						this.activeStep = 'success-material-options';
					}
				}
			}
			else
			{
				const myDiv = document.getElementById('containerWithScroll');
				myDiv.scrollTop = 0;

				this.activeStep = stepData.nextStep;
			}
		},
	}
};
</script>
