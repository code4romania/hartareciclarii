<template>
	<div class="">
		<div class="flex items-center justify-between">
			<h3 class="text-2xl" id="modal-title">{{CONSTANTS.LABELS.AUTH.REGISTER_FORM_TITLE}}</h3>
			<button v-on:click="closeModal();">
				<desktop-filter-close-icon></desktop-filter-close-icon>
			</button>
		</div>

		<div class="mt-3 sm:mt-5 w-5/6">
			<div class="space-y-1 mb-3">
				<label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{CONSTANTS.LABELS.AUTH.REGISTER.FIRSTNAME}}</label>
				<div class="mt-2">
					<input
						type="text"
						name="firstname"
						id="firstname"
						v-model="requestBody.firstname"
						class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
						:placeholder="CONSTANTS.LABELS.AUTH.REGISTER.FIRSTNAME_PLACEHOLDER"
					/>
				</div>
			</div>
			<template v-if="getError('firstname')">
				<div class="rounded-md bg-red-50 p-4 mb-2 mt-5">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800">{{CONSTANTS.LABELS.AUTH.REGISTER.ERRORS.FIRSTNAME}}</h3>
						</div>
					</div>
				</div>
			</template>

			<div class="space-y-1 mb-3">
				<label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{CONSTANTS.LABELS.AUTH.REGISTER.LASTNAME}}</label>
				<div class="mt-2">
					<input
						type="text"
						name="lastname"
						id="lastname"
						v-model="requestBody.lastname"
						class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
						:placeholder="CONSTANTS.LABELS.AUTH.REGISTER.LASTNAME_PLACEHOLDER"
					/>
				</div>
			</div>
			<template v-if="getError('lastname')">
				<div class="rounded-md bg-red-50 p-4 mb-2 mt-5">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800">{{CONSTANTS.LABELS.AUTH.REGISTER.ERRORS.LASTNAME}}</h3>
						</div>
					</div>
				</div>
			</template>
			<div class="space-y-1 mb-3">
				<label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{CONSTANTS.LABELS.AUTH.REGISTER.EMAIL}}</label>
				<div class="mt-2">
					<input
						type="email"
						name="email"
						id="email"
						v-model="requestBody.email"
						class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
						:placeholder="CONSTANTS.LABELS.AUTH.REGISTER.EMAIL_PLACEHOLDER"
					/>
				</div>
			</div>
			<template v-if="getError('email')">
				<div class="rounded-md bg-red-50 p-4 mb-2 mt-5">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800">{{CONSTANTS.LABELS.AUTH.REGISTER.ERRORS.EMAIL}}</h3>
						</div>
					</div>
				</div>
			</template>
			<div class="space-y-1 mb-3">
				<label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{CONSTANTS.LABELS.AUTH.REGISTER.PASSWORD}}</label>
				<div class="mt-2">
					<input
						type="password"
						name="password"
						id="password"
						v-model="requestBody.password"
						class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
						:placeholder="CONSTANTS.LABELS.AUTH.REGISTER.PASSWORD_PLACEHOLDER"
					/>
				</div>
			</div>
			<template v-if="getError('password')">
				<div class="rounded-md bg-red-50 p-4 mb-2 mt-5">
					<div class="flex">
						<div class="flex-shrink-0">
							<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
						</div>
						<div class="ml-3">
							<h3 class="text-sm font-medium text-red-800">{{CONSTANTS.LABELS.AUTH.REGISTER.ERRORS.PASSWORD}}</h3>
						</div>
					</div>
				</div>
			</template>

			<div class="space-y-1 mb-3">
				<div class="relative flex items-start">
					<div class="flex h-6 items-center">
						<input id="accept_terms" aria-describedby="accept_terms-description" name="accept_terms" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" v-model="requestBody.accept_terms"/>
					</div>
					<div class="ml-3 text-sm leading-6">
						<label for="accept_terms" class="font-medium text-gray-900" v-html="CONSTANTS.LABELS.AUTH.REGISTER.ACCEPT_TERMS"></label>
					</div>
				</div>
			</div>
			<div class="space-y-1 mb-3">
				<div class="relative flex items-start">
					<div class="flex h-6 items-center">
						<input id="send_newsletter" aria-describedby="send_newsletter-description" name="send_newsletter" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" v-model="requestBody.send_newsletter"/>
					</div>
					<div class="ml-3 text-sm leading-6">
						<label for="send_newsletter" class="font-medium text-gray-900" v-html="CONSTANTS.LABELS.AUTH.REGISTER.REGISTER_TO_NEWSLETTER"></label>
					</div>
				</div>
			</div>
			<div class="mb-1 text-end">
				<a v-on:click="login();" class="cursor-pointer">{{CONSTANTS.LABELS.AUTH.RECOVER}}</a>
			</div>
			<button
				type="button"
				class="inline-flex w-full justify-center rounded-md border border-black bg-black px-3 py-2 text-sm text-white shadow-sm sm:col-start-2"
				v-on:click="register();"
			>
				{{ CONSTANTS.LABELS.AUTH.REGISTER.SAVE }}</button>
		</div>
	</div>
</template>
<script>
import {CONSTANTS} from "@/constants";
import DesktopFilterCloseIcon from "../svg-icons/desktopFilterCloseIcon.vue";
import {XCircleIcon} from "@heroicons/vue/20/solid/index.js";
import _ from "lodash";
import {checkEmail, checkPassword} from "../../general.js";
import axios, {HttpStatusCode} from "axios";
export default
{
	components: {
		XCircleIcon,
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
		return {
			requestBody: {
				firstname: '',
				lastname: '',
				email: '',
				password: '',
				accept_terms: 0,
				send_newsletter: 0,
			},
			errors: {}
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
		},
		getError (key)
		{
			return _.get(this, ['errors', key], false);
		},
		validate ()
		{
			this.errors = {};

			if (!_.get(this, 'requestBody.firstname', '').length)
			{
				this.errors.firstname = true;
			}

			if (!_.get(this, 'requestBody.lastname', '').length)
			{
				this.errors.lastname = true;
			}

			if (!checkPassword(_.get(this, 'requestBody.password', '')))
			{
				this.errors.password = true;
			}

			if (!checkEmail(_.get(this, 'requestBody.email', '')))
			{
				this.errors.password = true;
			}

			return Object.keys(this.errors).length;
		},
		register()
		{
			if (this.validate())
			{
				console.log(this.errors);
				return;
			}

			let url = CONSTANTS.ROUTES.USER.PROFILE.REGISTER;
			axios
				.post(
					CONSTANTS.API_DOMAIN + url, this.requestBody
				)
				.then((response) => {
					this.$router.push('/');
				})
				.catch((err) => {
					//this.errors.password = CONSTANTS.LABELS.AUTH.REGISTER.ERRORS.API;
				});
		},
	}
};
</script>