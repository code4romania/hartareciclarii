<template>
	<div class="">
		<div class="flex items-center justify-between">
			<h3 class="text-2xl pl-10 py-5" id="modal-title">{{CONSTANTS.LABELS.AUTH.RECOVER_FORM_TITLE}}</h3>
			<button v-on:click="closeModal();">
				<desktop-filter-close-icon></desktop-filter-close-icon>
			</button>
		</div>

		<div class="mt-3 sm:mt-5 w-full px-10">
			<div class="space-y-1 mb-3">
				<label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{CONSTANTS.LABELS.AUTH.EMAIL}}</label>
				<div class="mt-2">
					<input
						type="email"
						name="email"
						id="email"
						class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
						:placeholder="CONSTANTS.LABELS.AUTH.EMAIL_PLACEHOLDER"
					/>
				</div>
			</div>

			<div class="mb-1 text-end">
				<a v-on:click="login();" class="cursor-pointer">{{CONSTANTS.LABELS.AUTH.RECOVER}}</a>
			</div>
			<button type="button" class="inline-flex w-full justify-center rounded-md border border-black bg-black px-3 py-2 text-sm text-white shadow-sm sm:col-start-2">Următorul pas</button>
		</div>
	</div>
</template>
<script>
import {CONSTANTS} from "@/constants";
import DesktopFilterCloseIcon from "../svg-icons/desktopFilterCloseIcon.vue";
export default
{
	components: {
		DesktopFilterCloseIcon,
	},
	props: {
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
		},
		login()
		{
			this.$emit('login');
		}
	}
};
</script>