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
		class="fixed w-full z-10 inset-y-0  lg:w-96 flex-col bg-white"
        ref="filtersBox"
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
                        v-model="searchParamsForFilters.search_key"
                        @input="pointLiveSearch()"
					>
				</div>
			</div>

            <span v-if="hasResults">
                <div class="px-4 pb-4 mb-3 border-b">
                    <span class="font-medium text-gray-900">{{ CONSTANTS.LABELS.SIDEBAR.FILTERS_TITLE }}</span>
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
                        <div class="space-y-1">
                            <template v-for="filter of filters.filters">
                                <div class="flex items-center">
                                    <input
                                        :id="'filter-service_' + filter.id"
                                        id="filter-service-1"
                                        class="h-4 w-4 rounded border-gray-300 text-secondary focus:ring-indigo-500"
                                        type="checkbox"
                                        value="1"
                                        @change="serviceFilterChanged(filter.id)"
                                        :checked="searchParamsForFilters.service_id === filter.id"
                                    >
                                    <label
                                        class="ml-2 min-w-0 flex-1 text-gray-700"
                                        :for="'filter-service_' + filter.id"
                                    >
                                        {{ filter.display_name }}
                                    </label>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div v-if="getFilter('service_types')">
                    <div class="px-4 pb-4 mb-3 border-b">
                        <span class="font-medium text-gray-900">{{ CONSTANTS.LABELS.SIDEBAR.COLLECTION_POINT_TYPE_LABEL}}</span>
                    </div>

                    <div class="px-6 pb-6">
                        <div id="filter-section-service" class="pt-4 pb-6 border-b">
                            <div class="space-y-1">
                                <template v-for="collectionPointFilter of getFilter('service_types')">
                                    <div class="flex items-center">
                                        <input
                                            :id="'colection_point_filter_' + collectionPointFilter.id"
                                            class="h-4 w-4 rounded border-gray-300 text-secondary focus:ring-indigo-500"
                                            type="checkbox"
                                            value="1"
                                            @change="collectionPointFilterChanged(collectionPointFilter)"
                                            :checked="selectedCollectionPointsTypes.includes(collectionPointFilter.id)"
                                        >
                                        <label
                                            class="ml-2 min-w-0 flex-1 text-gray-700"
                                            :for="'colection_point_filter_' + collectionPointFilter.id"
                                        >{{ collectionPointFilter.display_name }}</label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="getFilter('material_types')">
                    <div class="px-4 pb-4 mb-3 border-b">
                        <span class="font-medium text-gray-900">{{ CONSTANTS.LABELS.SIDEBAR.MATERIAL_TYPE_LABEL}}</span>
                    </div>

                    <div class="relative rounded-md shadow-sm m-3">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <mobile-filter-scope-icon></mobile-filter-scope-icon>
                        </div>
                        <input
                            id="search-point"
                            :placeholder="CONSTANTS.LABELS.SIDEBAR.SEARCH_MATERIAL_LABEL"
                            class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="text"
                            type="email"
                            v-model="materialFilterLiveSearch"
                            @input="materialLiveSearch()"
                        >
                    </div>

                    <div class="px-6 pb-6">
                        <div id="filter-section-service" class="pt-4 pb-6 border-b">
                            <div class="space-y-1">
                                <template v-for="materialFilter of materialTypesFilters">
                                    <div class="flex items-center">
                                        <input
                                            :id="'material_filter_' + materialFilter.id"
                                            class="h-4 w-4 rounded border-gray-300 text-secondary focus:ring-indigo-500"
                                            type="checkbox"
                                            value="1"
                                            @change="materialFilterChanged(materialFilter, true)"
                                            :checked="selectedMaterialTypes.includes(materialFilter.id)"
                                        >
                                        <label
                                            class="ml-2 min-w-0 flex-1 text-gray-700"
                                            :for="'material_filter_' + materialFilter.id"
                                        >{{ materialFilter.name }}</label>
                                    </div>
                                    <template v-for="childrenMaterialFilter of materialFilter.children">
                                        <div class="flex items-center ml-8">
                                            <input
                                                :id="'material_filter_' + childrenMaterialFilter.id"
                                                class="h-4 w-4 rounded border-gray-300 text-secondary focus:ring-indigo-500"
                                                type="checkbox"
                                                @change="materialFilterChanged(childrenMaterialFilter, false)"
                                                value="1"
                                                :checked="selectedMaterialTypes.includes(childrenMaterialFilter.id)"
                                            >
                                            <label
                                                class="ml-2 min-w-0 flex-1 text-gray-700"
                                                :for="'material_filter_' + childrenMaterialFilter.id"
                                            >{{ childrenMaterialFilter.name }}</label>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </span>

            <span v-else>
                <div class="px-4 pb-4 mb-3 border-b">
                    0 {{ CONSTANTS.LABELS.SIDEBAR.RESULTS }}
                </div>
                <div class="px-6 pb-6">
                    <img src="/assets/images/notFound.png" style=" display: block;margin-left: auto;margin-right: auto;">

                    <p class="block text-xs font-normal leading-6 text-gray-900 mt-5 text-center">
                        {{ CONSTANTS.LABELS.SIDEBAR.NO_RESULTS_FOUND_FIRST_PART }} "{{ searchParamsForFilters.search_key }}" <br>
                        {{ CONSTANTS.LABELS.SIDEBAR.NO_RESULTS_FOUND_SECOND_PART }}
                    </p>

                    <div class="text-center">
                        <button
                            v-on:click="resetFilters()"
                            type="button"
                            class=" mt-5 rounded-full bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                        >
                            {{ CONSTANTS.LABELS.SIDEBAR.SEE_ALL_POINTS }}
                        </button>
                    </div>
                </div>
            </span>

			<div class="px-6 py-3 border-t absolute bottom-0 bg-white w-full">
				<button class="flex items-center justify-center text-red-700 w-full" type="button" v-on:click="resetFilters">
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
import axios from "axios";
import _ from "lodash";

export default {
	components: {MobileFilterScopeIcon, DesktopFilterCloseIcon, DesktopFilterScopeIcon, DesktopFilterBurgerIcon, DesktopFilterClearIcon},
	computed: {
		CONSTANTS () {
			return CONSTANTS;
		},
	},
    props: {
        hasResults: {
            type: Boolean,
            required: false,
            default: true
        },
    },
	data () {
		return {
			open: false,
            filters: {},
            pointFilterLiveSearch: '',
            materialFilterLiveSearch: '',
            selectedMaterialTypes: [],
            selectedCollectionPointsTypes: [],
			filtersCount: 0,
            materialTypesFilters: [],
            searchParamsForFilters: {
                search_key: '',
                service_id: null
            },
			collectedFilters: {}
		};
	},
    mounted() {
        this.getFilters();
    },
    methods: {
		toggle () {
			this.open = !this.open;
		},
		toggleFilters() {
			this.open = !this.open;
		},
        getFilter(filterType) {
            return _.get(this, ['filters', 'extended_filters', filterType], false);
        },
        convertFiltersToTree(filterType) {
            this.materialTypesFilters = [];
            for (const filter of _.get(this, ['filters', 'extended_filters', filterType], [])) {
                if (!filter.parent) {
                    let filterToAppend = filter;
                    filterToAppend.children = [];
                    for (const childrenFilter of _.get(this, ['filters', 'extended_filters', filterType], [])) {
                        if (childrenFilter.parent === filter.id) {
                            filterToAppend.children.push(childrenFilter);
                        }
                    }
                    this.materialTypesFilters.push(filterToAppend);
                }
            }
			this.collectedFilters.material_type_id = this.materialTypesFilters;
        },
        serviceFilterChanged(filterId) {
            if (filterId === this.searchParamsForFilters.service_id) {
                this.searchParamsForFilters.service_id = null;
				this.collectedFilters.service_id = null;
                this.getFilters();
                return;
            }

            this.searchParamsForFilters.service_id = filterId
			this.collectedFilters.service_id = filterId;
            this.getFilters();
        },
        materialFilterChanged(filter, isParent) {
            if (isParent) {
                if (this.selectedMaterialTypes.includes(filter.id)) {
                    this.selectedMaterialTypes = this.selectedMaterialTypes.filter(item => item !== filter.id)

                    for (const child of filter.children) {
                        if (this.selectedMaterialTypes.includes(child.id)) {
                            this.selectedMaterialTypes = this.selectedMaterialTypes.filter(item => item !== child.id)
                        }
                    }
					this.collectedFilters.material_type_id = this.selectedMaterialTypes;
                    return;
                }

                this.selectedMaterialTypes.push(filter.id);
                for (const child of filter.children) {
                    this.selectedMaterialTypes.push(child.id);
                }

				this.collectedFilters.material_type_id = this.selectedMaterialTypes;
                return;
            }


            if (this.selectedMaterialTypes.includes(filter.id)) {
                this.selectedMaterialTypes = this.selectedMaterialTypes.filter(item => item !== filter.id)
				this.collectedFilters.material_type_id = this.selectedMaterialTypes;
                return;
            }

            this.selectedMaterialTypes.push(filter.id);
			//this.collectedFilters.material_type_id = this.selectedMaterialTypes;
        },
        collectionPointFilterChanged(filter) {
            if (this.selectedCollectionPointsTypes.includes(filter.id)) {
                this.selectedCollectionPointsTypes = this.selectedCollectionPointsTypes.filter(item => item !== filter.id)
				this.collectedFilters.field_type_id = this.selectedCollectionPointsTypes;
                return;
            }

            this.selectedCollectionPointsTypes.push(filter.id);
			this.collectedFilters.field_type_id = this.selectedCollectionPointsTypes;
        },
        getFilters() {
            const loader = this.$loading.show({
                container: this.$refs.filtersBox
            });

            axios
                .get(
                    CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.STATIC.FILTERS.FILTERS,
                    {
                        params: this.searchParamsForFilters
                    }
                )
                .then((response) => {
                    this.filters = _.get(response, 'data', {});
                    this.convertFiltersToTree('material_types');
                    loader.hide()
                })
                .catch((err) => {
                    loader.hide()
                });
        },
        pointLiveSearch() {
            if (this.searchParamsForFilters.search_key.length % 3 === 0) {
                this.getFilters();
            }
        },
        resetFilters() {
			this.searchParamsForFilters = {};
			this.filters.extended_filters = {};
        },
        materialLiveSearch() {
            this.selectedMaterialTypes = [];

            if (this.materialFilterLiveSearch.length % 2 === 0) {
                if (!this.materialFilterLiveSearch.length) {
                    this.convertFiltersToTree('material_types');
                    return;
                }

                let parentsToDelete = [];
                for (const materialTypeFilterKey in this.materialTypesFilters) {
                    if (_.get(this, ['materialTypesFilters', materialTypeFilterKey, 'children'], []).length) {
                        this.materialTypesFilters[materialTypeFilterKey].children = this.materialTypesFilters[materialTypeFilterKey].children
                            .filter(item => item.name.includes(this.materialFilterLiveSearch))
                    }
                    if (!_.get(this, ['materialTypesFilters', materialTypeFilterKey, 'children'], []).length) {
                        if (!this.materialTypesFilters[materialTypeFilterKey].name.includes(this.materialFilterLiveSearch)) {
                            parentsToDelete.push(materialTypeFilterKey);
                        }
                    }
                }

                const newFilters = [];
                for (const materialTypeFilterKey in this.materialTypesFilters) {
                    if (!parentsToDelete.includes(materialTypeFilterKey)) {
                        newFilters.push(this.materialTypesFilters[materialTypeFilterKey]);
                    }
                }
                this.materialTypesFilters = newFilters;
            }
        }
	},
	watch: {
		collectedFilters: {
			handler: function (newVal)
			{
				this.$emit('filters-changed', this.collectedFilters);
			},
			deep: true,
			immediate: true
		}
	},
};
</script>
