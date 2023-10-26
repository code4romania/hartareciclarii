<template>
	<!-- Static sidebar for desktop sm -->
	<div
		class="hidden lg:fixed lg:inset-y-0 lg:z-10 lg:w-[4.5rem] lg:flex-col bg-white"
		:class="{'lg:flex': !open, 'lg:hidden': open}"
	>
		<!-- Toggle lg:hidden lg:flex -->
		<div class="p-4">
			<button class="rounded bg-gray-50 text-primary w-10 h-10 flex items-center justify-center cursor-pointer" type="button" v-on:click="toggleFilters();">
				<desktop-filter-burger-icon></desktop-filter-burger-icon>
			</button>
		</div>

		<div class="p-4">
			<button class="text-sm flex items-center flex-col justify-center w-full relative" type="button">
				<span
					class="p-1 rounded-full border-0 bg-green-500 text-end block absolute -top-0.5 end-2"
					:class="{'hidden': !hasSearchContent, '': hasSearchContent}"
				>

				</span>
				<desktop-filter-scope-icon></desktop-filter-scope-icon>
				<span class="mt-1">{{CONSTANTS.LABELS.SIDEBAR.SEARCH}}</span>
			</button>
		</div>

		<div class="p-4">
			<button class="text-sm flex items-center flex-col justify-center w-full relative" type="button">
				<span
					class="p-1 rounded-full border-0 bg-green-500 text-end block absolute -top-0.5 end-2"
					:class="{'hidden': !hasFiltersContent, '': hasFiltersContent}"
				>

				</span>
				<desktop-filter-filters-icon></desktop-filter-filters-icon>
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
					<desktop-filter-close-icon></desktop-filter-close-icon>
				</button>
			</div>

			<div class="p-4 mb-6 bg-gray-50">
				<label class="block text-sm font-medium leading-6 text-gray-900" for="search-point">{{ CONSTANTS.LABELS.SIDEBAR.SEARCH_POINT_LABEL }}</label>
				<div class="relative mt-2 rounded-md shadow-sm">
					<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
						<mobile-filter-scope-icon></mobile-filter-scope-icon>
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
					<desktop-filter-clear-icon></desktop-filter-clear-icon>
					{{ CONSTANTS.LABELS.SIDEBAR.CLEAR_FILTERS_LABEL }}
				</button>
			</div>

		</div>
	</div>

</template>

<script>
import {CONSTANTS} from "@/constants.js";
import DesktopFilterBurgerIcon from "./svg-icons/desktopFilterBurgerIcon.vue";
import DesktopFilterScopeIcon from "./svg-icons/desktopFilterScopeIcon.vue";
import DesktopFilterCloseIcon from "./svg-icons/desktopFilterCloseIcon.vue";
import DesktopFilterClearIcon from "./svg-icons/desktopFilterClearIcon.vue";
import MobileFilterScopeIcon from "./svg-icons/mobileFilterScopeIcon.vue";

export default {
	components: {MobileFilterScopeIcon, DesktopFilterCloseIcon, DesktopFilterScopeIcon, DesktopFilterBurgerIcon, DesktopFilterClearIcon},
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
			filtersCount: 0,
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