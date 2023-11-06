<template>
	<TransitionRoot :show="isOpen" as="template">
		<Dialog as="div" class="relative z-10" @close="closeModal();">
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
						<success-finish-step
							v-if="activeStep === 'success-finish'"
							:active-step="activeStep"
							:map-point="mapPoint"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="savePoint($event)"
						></success-finish-step>
						<!--
						<materials-options-step
							v-show="activeStep === 'materials-options'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="savePoint($event)"
						></materials-options-step>
						<materials-not-collected-step
							v-show="activeStep === 'materials-not-collected'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="savePoint($event)"
						></materials-not-collected-step>
						<materials-missing-step
							v-show="activeStep === 'materials-missing'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="savePoint($event)"
						></materials-missing-step>
						<materials-other-step
							v-show="activeStep === 'materials-other'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="savePoint($event)"
						></materials-other-step>
						<schedule-step
							v-show="activeStep === 'schedule'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="savePoint($event)"
						></schedule-step>
						<container-step
							v-show="activeStep === 'container'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="savePoint($event)"
						></container-step>
						<takeover-step
							v-show="activeStep === 'takeover'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="savePoint($event)"
						></takeover-step>
						<other-problem-step
							v-show="activeStep === 'other-problem'"
							:active-step="activeStep"
							:nomenclatures="nomenclatures"
							:previous-step-body="requestBody"
							@backToStep="backToStep($event)"
							@close="closeModal();"
							@stepFinished="savePoint($event)"
						></other-problem-step>
						-->
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
import SuccessFinishStep from "./successFinishStep.vue";

export default {
	components: {
		SuccessFinishStep,
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
			const myDiv = document.getElementById('containerWithScroll');
			myDiv.scrollTop = 0;

			this.activeStep = stepData.nextStep;
			this.requestBody = {...this.requestBody, ...stepData.body};

			console.log(`stepFinished`, stepData, this.requestBody);
		},
		savePoint ()
		{
			axios
				.post(
					CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.MAP.POINTS.CREATE,
					this.requestBody
				)
				.then((response) =>
				{
					if (_.get(response, 'status', 0) === HttpStatusCode.Ok)
					{
						this.closeModal();
						this.resetModal();
						this.$emit('pointSaved', true);
					}
				})
				.catch((err) =>
				{
				});
		}
	}
};
</script>
