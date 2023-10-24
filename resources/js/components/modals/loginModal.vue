<template>
	<TransitionRoot as="template" :show="isOpen">
		<Dialog as="div" class="relative z-10" @close="closeModal();">
			<TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
				<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
			</TransitionChild>

			<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

			<div class="fixed inset-0 z-50 w-screen overflow-y-auto">
				<div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
					<!--
					  Modal panel, show/hide based on modal state.

					  Entering: "ease-out duration-300"
						From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
						To: "opacity-100 translate-y-0 sm:scale-100"
					  Leaving: "ease-in duration-200"
						From: "opacity-100 translate-y-0 sm:scale-100"
						To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
					-->
					<div class="relative transform overflow-hidden flex flex-col rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:w-full sm:max-w-lg sm:p-6 h-[80vh]">
						<div class="">
							<div class="flex items-center justify-between">
								<h3 class="text-2xl" id="modal-title">Raportează o problemă</h3>
								<button v-on:click="closeModal();">
									<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M1 13L13 1M1 1L13 13" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</button>
							</div>

							<div class="mt-3 sm:mt-5">
								<p class="text-gray-700 mb-4">Ce tip de problemă ai identificat?</p>

								<div class="space-y-2">
									<div class="flex items-center">
										<input id="email" name="notification-method" type="radio" checked class="h-4 w-4 border-gray-400 text-black focus:ring-indigo-600">
										<label for="email" class="ml-3 block text-sm leading-5 font-medium text-gray-700">Email</label>
									</div>
									<div class="flex items-center">
										<input id="sms" name="notification-method" type="radio" class="h-4 w-4 border-gray-400 text-black focus:ring-indigo-600">
										<label for="sms" class="ml-3 block text-sm leading-5 font-medium text-gray-700">Phone (SMS)</label>
									</div>
									<div class="flex items-center">
										<input id="push" name="notification-method" type="radio" class="h-4 w-4 border-gray-400 text-black focus:ring-indigo-600">
										<label for="push" class="ml-3 block text-sm leading-5 font-medium text-gray-700">Push notification</label>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-auto sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
							<button type="button" class="inline-flex w-full justify-center rounded-md border border-black bg-black px-3 py-2 text-sm text-white shadow-sm sm:col-start-2">Următorul pas</button>
							<button type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-black bg-white px-3 py-2 text-sm text-black shadow-sm sm:col-start-1 sm:mt-0">Renunță</button>
						</div>
					</div>
				</div>
			</div>
		</Dialog>
	</TransitionRoot>
</template>

<script>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/24/outline'
import {CONSTANTS} from "@/constants";
export default
{
	components: {
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
		return
		{
		}
	},
	methods:
	{
		closeModal ()
		{
			this.$emit('close');
		}
	}
};
</script>