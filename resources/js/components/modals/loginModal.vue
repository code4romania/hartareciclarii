<template>
	<TransitionRoot as="template" :show="isOpen">
		<Dialog as="div" class="relative z-10" @close="closeModal();">
			<TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
				<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
			</TransitionChild>

			<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
			<div class="fixed inset-0 z-50 w-screen overflow-y-auto">
				<div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
					<div class="relative transform overflow-hidden flex flex-col rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:w-full sm:max-w-lg sm:p-6 h-[80vh]">
						<login-form
							v-if="showLogin"
							@close="closeModal();"
							@recover="showRecover = true; showLogin = false; showRegister = false"
							@register="showRecover = false; showLogin = false; showRegister = true"
						></login-form>
						<recover-form
							v-if="showRecover"
							@close="closeModal();"
							@login="showRecover = false; showLogin= true; showRegister = false"
							@register="showRecover = false; showLogin = false; showRegister = true"
						></recover-form>
						<register-form
							v-if="showRegister"
							@close="closeModal();"
						></register-form>
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
import DesktopFilterCloseIcon from "../svg-icons/desktopFilterCloseIcon.vue";
import LoginForm from "../partials/loginForm.vue";
import RecoverForm from "../partials/recoverForm.vue";
import RegisterForm from "../partials/registerForm.vue";
export default
{
	components: {
		RegisterForm,
		RecoverForm,
		LoginForm,
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
		}
	},
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS;
		}
	},
	data()
	{
		return {
			showLogin: true,
			showRecover: false,
			showRegister: false,
		};
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