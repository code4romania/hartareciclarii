<template>
	<!-- Static sidebar for desktop sm -->
	<div
		class="hidden lg:fixed lg:inset-y-0 lg:z-10 lg:w-[4.5rem] lg:flex-col bg-white"
		:class="{'lg:flex': !open, 'lg:hidden': open}"
	>
		<!-- Toggle lg:hidden lg:flex -->
		<div class="p-4">
			<button class="rounded bg-gray-50 text-primary w-10 h-10 flex items-center justify-center cursor-pointer" type="button" v-on:click="toggleFilters();">
				<svg fill="none" height="14" viewBox="0 0 18 14" width="18" xmlns="http://www.w3.org/2000/svg">
					<path d="M1 1H17M1 7H17M1 13H17" stroke="#00AD4D" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
				</svg>
			</button>
		</div>

		<div class="p-4">
			<button class="text-sm flex items-center flex-col justify-center w-full relative" type="button">
				<span class="bg-primary p-1 rounded-full border-2 border-gray-50 text-end block absolute -top-0.5 end-2"></span>
				<svg fill="none" height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg">
					<path
						d="M19 19L13 13M15 8C15 11.866 11.866 15 8 15C4.13401 15 1 11.866 1 8C1 4.13401 4.13401 1 8 1C11.866 1 15 4.13401 15 8Z"
						stroke="#6B7280" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
				</svg>
				<span class="mt-1">{{CONSTANTS.LABELS.SIDEBAR.SEARCH}}</span>
			</button>
		</div>

		<div class="p-4">
			<button class="text-sm flex items-center flex-col justify-center w-full relative" type="button">
				<span
					class="bg-primary p-1 rounded-full border-2 border-gray-50 text-end block absolute -top-0.5 end-2"></span>
				<svg fill="none" height="18" viewBox="0 0 18 18" width="18" xmlns="http://www.w3.org/2000/svg">
					<path
						d="M9 3V1M9 3C7.89543 3 7 3.89543 7 5C7 6.10457 7.89543 7 9 7M9 3C10.1046 3 11 3.89543 11 5C11 6.10457 10.1046 7 9 7M3 15C4.10457 15 5 14.1046 5 13C5 11.8954 4.10457 11 3 11M3 15C1.89543 15 1 14.1046 1 13C1 11.8954 1.89543 11 3 11M3 15V17M3 11V1M9 7V17M15 15C16.1046 15 17 14.1046 17 13C17 11.8954 16.1046 11 15 11M15 15C13.8954 15 13 14.1046 13 13C13 11.8954 13.8954 11 15 11M15 15V17M15 11V1"
						stroke="#6B7280" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/>
				</svg>
				<span class="mt-1">{{CONSTANTS.LABELS.SIDEBAR.FILTERS}}</span>
			</button>
		</div>
	</div>
	<!-- END Static sidebar for desktop sm -->

	<!-- Static sidebar for desktop -->
	<div
		class="fixed w-full z-10 inset-y-0  lg:w-72 flex-col bg-white"
		:class="{'flex': open, 'hidden': !open}"
	>
		<!-- Sidebar component, swap this element with another sidebar if you like -->
		<div class="flex grow flex-col overflow-y-auto">
			<div class="flex h-16 row items-center justify-between p-4 bg-gray-50">
				<a href="">
					<img alt="Harta Reciclarii V2.0" class="h-8 w-auto" src="/assets/images/logo.png">
				</a>
				<button type="button" v-on:click="toggleFilters();" class=" cursor-pointer">
					<svg fill="none" height="14" viewBox="0 0 14 14" width="14" xmlns="http://www.w3.org/2000/svg">
						<path d="M1 13L13 1M1 1L13 13" stroke="#9CA3AF" stroke-linecap="round" stroke-linejoin="round"
							  stroke-width="2"/>
					</svg>
				</button>
			</div>

			<div class="p-4 mb-6 bg-gray-50">
				<label class="block text-sm font-medium leading-6 text-gray-900" for="search-point">{{ CONSTANTS.LABELS.SIDEBAR.SEARCH_POINT_LABEL }}</label>
				<div class="relative mt-2 rounded-md shadow-sm">
					<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
						<svg fill="none" height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg">
							<path clip-rule="evenodd" d="M8 4C5.79086 4 4 5.79086 4 8C4 10.2091 5.79086 12 8 12C10.2091 12 12 10.2091 12 8C12 5.79086 10.2091 4 8 4ZM2 8C2 4.68629 4.68629 2 8 2C11.3137 2 14 4.68629 14 8C14 9.29583 13.5892 10.4957 12.8907 11.4765L17.7071 16.2929C18.0976 16.6834 18.0976 17.3166 17.7071 17.7071C17.3166 18.0976 16.6834 18.0976 16.2929 17.7071L11.4765 12.8907C10.4957 13.5892 9.29583 14 8 14C4.68629 14 2 11.3137 2 8Z"
								  fill="#9CA3AF"
								  fill-rule="evenodd"/>
						</svg>
					</div>
					<input
						id="search-point"
						:placeholder="CONSTANTS.LABELS.SIDEBAR.SEARCH_POINT_PLACEHOLDER"
						class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
						name="text"
						type="email"
					>
				</div>
			</div>

			<div class="px-4 pb-4 mb-3 border-b">
				<span class="font-medium text-gray-900">{{ CONSTANTS.LABELS.SIDEBAR.FILTERS_TITLE}}</span>
			</div>

			<div class="px-6 pb-6">
				<h3 class="flow-root">
					<!-- Expand/collapse section button -->
					<button aria-controls="filter-section-mobile-0"
							aria-expanded="false"
							class="flex w-full items-center justify-between bg-white text-gray-400 hover:text-gray-500" type="button">
						<span class="font-medium text-gray-900">{{ CONSTANTS.LABELS.SIDEBAR.SERVICE_TYPE_LABEL }}</span>

					</button>
				</h3>
				<!-- Filter section, show/hide based on section state. -->
				<div id="filter-section-service" class="pt-4 pb-6 border-b">
					<div class="space-y-4">
						<div class="flex items-center">
							<input id="filter-service-1" class="h-4 w-4 rounded border-gray-300 text-secondary focus:ring-indigo-500" name="service[]" type="checkbox"
								   value="1">
							<label class="ml-2 min-w-0 flex-1 text-gray-700" for="filter-service-1">Colectare selectiva
								deseuri</label>
						</div>
						<div class="flex items-center">
							<input id="filter-service-2" class="h-4 w-4 rounded border-gray-300 text-secondary focus:ring-indigo-500" name="service[]" type="checkbox"
								   value="2">
							<label class="ml-2 min-w-0 flex-1 text-gray-700" for="filter-service-2">Magazin zero
								waste</label>
						</div>
						<div class="flex items-center">
							<input id="filter-service-3" checked class="h-4 w-4 rounded border-gray-300 text-secondary focus:ring-indigo-500" name="service[]" type="checkbox"
								   value="3">
							<label class="ml-2 min-w-0 flex-1 text-gray-700" for="filter-service-3">Atelier de
								reparatii</label>
						</div>
						<div class="flex items-center">
							<input id="filter-service-4" class="h-4 w-4 rounded border-gray-300 text-secondary focus:ring-indigo-500" name="service[]" type="checkbox"
								   value="4">
							<label class="ml-2 min-w-0 flex-1 text-gray-700" for="filter-service-4">Lorem ipsum</label>
						</div>
					</div>
				</div>
			</div>
			<div class="px-6 py-3 border-t absolute bottom-0 bg-white w-full">
				<button class="flex items-center justify-center text-red-700 w-full" type="button">
					<svg fill="none" height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg">
						<path clip-rule="evenodd" d="M4.29289 4.29289C4.68342 3.90237 5.31658 3.90237 5.70711 4.29289L10 8.58579L14.2929 4.29289C14.6834 3.90237 15.3166 3.90237 15.7071 4.29289C16.0976 4.68342 16.0976 5.31658 15.7071 5.70711L11.4142 10L15.7071 14.2929C16.0976 14.6834 16.0976 15.3166 15.7071 15.7071C15.3166 16.0976 14.6834 16.0976 14.2929 15.7071L10 11.4142L5.70711 15.7071C5.31658 16.0976 4.68342 16.0976 4.29289 15.7071C3.90237 15.3166 3.90237 14.6834 4.29289 14.2929L8.58579 10L4.29289 5.70711C3.90237 5.31658 3.90237 4.68342 4.29289 4.29289Z"
							  fill="#EF4444"
							  fill-rule="evenodd"/>
					</svg>
					{{ CONSTANTS.LABELS.SIDEBAR.CLEAR_FILTERS_LABEL }}
				</button>
			</div>

		</div>
	</div>

</template>

<script>
import {CONSTANTS} from "@/constants.js";

export default {
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS;
		}
	},
	data ()
	{
		return {
			open: false,
			filtersCount: 0
		};
	},
	methods: {
		toggle ()
		{
			this.open = !this.open;
		},
		toggleFilters()
		{
			this.open = !this.open;
		}
	}
};
</script>