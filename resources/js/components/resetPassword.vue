<template>
	<div class="flex h-screen">
		<div class="m-auto w-96">
			<form>
				<div class="space-y-12">
					<div class="border-b border-gray-900/10 pb-12">
						<h2 class="text-base font-semibold leading-7 text-gray-900">{{ CONSTANTS.LABELS.AUTH.RESET_LABEL }}</h2>
						<p class="mt-1 text-sm leading-6 text-gray-600">{{CONSTANTS.LABELS.AUTH.RESET_LABEL_HEADING}}</p>

						<div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
							<div class="sm:col-span-4">
								<label for="username" class="block text-sm font-medium leading-6 text-gray-900">{{ CONSTANTS.LABELS.AUTH.PASSWORD }}</label>
								<div class="mt-2">
									<input type="password" name="password" id="password" autocomplete="off" class="rounded-md py-1.5 pl-1" placeholder="" v-model="requestBody.password" />
								</div>
							</div>
						</div>
						<div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
							<div class="sm:col-span-4">
								<label for="username" class="block text-sm font-medium leading-6 text-gray-900">{{ CONSTANTS.LABELS.AUTH.PASSWORD_CONFIRM }}</label>
								<div class="mt-2">
									<input type="password" name="confirm_password" id="confirm_password" autocomplete="off" class="rounded-md py-1.5 pl-1" placeholder="" v-model="requestBody.confirm_password" />
								</div>
							</div>
						</div>
						<template v-if="getError('password')">
							<div class="rounded-md bg-red-50 p-4 mb-2 mt-5">
								<div class="flex">
									<div class="flex-shrink-0">
										<XCircleIcon aria-hidden="true" class="h-5 w-5 text-red-400"/>
									</div>
									<div class="ml-3">
										<h3 class="text-sm font-medium text-red-800">
											{{ errors.password }}</h3>
									</div>
								</div>
							</div>
						</template>
					</div>
				</div>
				<div class="mt-6 flex items-center justify-end gap-x-6">
					<button
						class="rounded bg-white px-2 py-1 text-sm font-semibold text-gray-600 shadow-sm hover:bg-gray-200 border border-black h-10 mr-3"
						type="button"
						v-on:click="$router.push('/')"
					>
						{{ CONSTANTS.LABELS.AUTH.CANCEL }}
					</button>
					<button
						class="rounded bg-black px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 border border-black h-10"
						type="button"
						v-on:click="reset()"
					>
						{{ CONSTANTS.LABELS.AUTH.SAVE }}
					</button>
				</div>
			</form>
		</div>
	</div>

</template>

<script>
import {CONSTANTS} from "../constants.js";
import moment from 'moment';
import _ from "lodash";
import {XCircleIcon} from "@heroicons/vue/20/solid/index.js";
import {checkPassword} from "../general.js";
import axios, {HttpStatusCode} from "axios";

export default
{
	components:
	{
		XCircleIcon

	},
	data ()
	{
		return {
			moment,
			requestBody: {
				'password': '',
				'confirm_password': '',
			},
			errors: {}
		};
	},
	methods:
	{
		getError (key)
		{
			return _.get(this, ['errors', key], false);
		},
		reset()
		{
			if (this.validate())
			{
				return;
			}


			console.log(`resetting`, this.$route.params.token);

			let url = CONSTANTS.ROUTES.AUTH.RECOVER_CONFIRM;
			axios
				.post(
					CONSTANTS.API_DOMAIN + url,
					{
						password: this.requestBody.password,
						token: this.$route.params.token,
						password_confirmation: this.requestBody.confirm_password,
					}
				)
				.then((response) => {
					if (_.get(response, 'status', 0) === HttpStatusCode.Ok) {
						this.$router.push('/');
					}
				})
				.catch((err) => {
					this.errors.password = CONSTANTS.LABELS.AUTH.RESET.ERRORS.INVALID_TOKEN;
				});
		},
		validate ()
		{
			this.errors = {};

			if (!checkPassword(_.get(this, 'requestBody.password', '')))
			{
				this.errors.password = CONSTANTS.LABELS.AUTH.RESET.ERRORS.INVALID_PASSWORD;
			}

			if (_.get(this, 'requestBody.password', '') !== _.get(this, 'requestBody.confirm_password', ''))
			{
				this.errors.confirm_password = CONSTANTS.LABELS.AUTH.RESET.ERRORS.PASSWORDS_NOT_MATCH;
			}

			return Object.keys(this.errors).length;
		},
	},
	mounted ()
	{

	},
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS
		}
	}
};
</script>

