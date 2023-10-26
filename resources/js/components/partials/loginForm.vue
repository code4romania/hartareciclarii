<template>
	<div class="">
		<div class="flex items-center justify-between">
			<h3 id="modal-title" class="text-2xl pl-10 py-5">{{ CONSTANTS.LABELS.AUTH.LOGIN_FORM_TITLE }}</h3>
			<button v-on:click="closeModal();">
				<desktop-filter-close-icon></desktop-filter-close-icon>
			</button>
		</div>

		<div class="mt-3 sm:mt-5 w-full px-10">
			<div class="space-y-1 mb-3">
				<label class="block text-sm font-medium leading-6 text-gray-900"
					   for="email">{{ CONSTANTS.LABELS.AUTH.EMAIL }}</label>
				<div class="mt-2">
					<input
						id="email"
						v-model="loginData.email"
						:placeholder="CONSTANTS.LABELS.AUTH.EMAIL_PLACEHOLDER"
						class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
						name="email"
						required
						type="email"
					/>
				</div>
			</div>
			<div class="space-y-1 mb-1">
				<label class="block text-sm font-medium leading-6 text-gray-900"
					   for="email">{{ CONSTANTS.LABELS.AUTH.PASSWORD }}</label>
				<div class="mt-2">
					<input
						id="password"
						v-model="loginData.password"
						:placeholder="CONSTANTS.LABELS.AUTH.PASSWORD_PLACEHOLDER"
						class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
						name="password"
						required
						type="password"
					/>
				</div>
			</div>
			<div class="mb-1 text-end">
				<a class="cursor-pointer" v-on:click="recover();">{{ CONSTANTS.LABELS.AUTH.RECOVER }}</a>
			</div>
			<template v-if="formHasErrors">
				<div class="rounded-md bg-red-50 p-4">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800">Eroare</h3>
							<div class="mt-2 text-sm text-red-700">
								{{ errorMessage }}
							</div>
						</div>
					</div>
				</div>
			</template>
			<div class="py-20 px-20 space-x-5 mb-1">
				<button
					id="login_button"
					:class="{ 'disabled': loading }"
					class="inline-flex w-full justify-center rounded-md border border-black bg-black px-3 py-2 text-sm text-white shadow-sm sm:col-start-2"
					type="button"
					v-on:click="login()"
				>
					{{ CONSTANTS.LABELS.AUTH.LOGIN_BUTTON }}
				</button>
			</div>
			<div class="relative">
				<div aria-hidden="true" class="absolute inset-0 flex items-center">
					<div class="w-full border-t border-gray-300"/>
				</div>
			</div>
			<div class="py-20 space-x-5 mb-1">
				{{ CONSTANTS.LABELS.AUTH.REGISTER_LABEL }} <a class="cursor-pointer underline" v-on:click="register();">{{ CONSTANTS.LABELS.AUTH.REGISTER_LABEL_LINK }}</a>
			</div>
		</div>
	</div>
</template>
<script>
import _ from 'lodash';
import {CONSTANTS} from "@/constants";
import axios, {HttpStatusCode} from "axios";
import DesktopFilterCloseIcon from "../svg-icons/desktopFilterCloseIcon.vue";
import {XCircleIcon} from '@heroicons/vue/20/solid';
import eventBus from "../../eventBus.js";

export default {
	components: {
		DesktopFilterCloseIcon, XCircleIcon
	},
	props: {},
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS;
		}
	},
	data ()
	{
		return {
			loading: false,
			formHasErrors: false,
			loginData: {
				email: "",
				password: ""
			},
			errorMessage: CONSTANTS.LABELS.AUTH.ERROR
		};
	},
	methods:
		{
			closeModal ()
			{
				this.$emit('close');
			},
			recover ()
			{
				this.$emit('recover');
			},
			register ()
			{
				this.$emit('register');
			},
			login ()
			{
				let button = document.getElementById('login_button');
				button.disabled = true;
				if (this.loginData.email != "" && this.loginData.password != "")
				{
					axios
						.post(
							CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.AUTH.LOGIN,
							this.loginData,
						).then((response) =>
					{
						if (_.get(response, 'status', 0) === HttpStatusCode.Ok)
						{
							localStorage.setItem(
								'userSession',
								JSON.stringify(_.get(response, 'data.user', {}))
							);
							localStorage.setItem(
								'userToken',
								JSON.stringify(_.get(response, ['data', 'authorization'], ''))
							);
						}
						button.disabled = false;
						eventBus.$emit('getUser', true);
						this.$emit('close');
					}).catch((err) =>
					{
						this.formHasErrors = true;
						this.errorMessage = _.get(CONSTANTS.LABELS.API, _.get(err.response.data, 'message', 'invalid_credentials'), CONSTANTS.LABELS.AUTH.ERROR);
						button.disabled = false;
					});
				}
				else
				{
					this.formHasErrors = true;
					this.errorMessage = CONSTANTS.LABELS.AUTH.ERROR;
					button.disabled = false;
				}
			}
		}
};
</script>