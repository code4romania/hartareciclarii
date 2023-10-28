<template>
	<left-sidebar
        :has-results="false"
    ></left-sidebar>

	<div
		:class="{'g:pl-[4.5rem]': !open, 'lg:pl-72': open}"
	>  <!-- Toggle lg:pl-[4.5rem] OR lg:pl-72 -->
		<main class="">
			<div class="flex absolute inset-x-0 px-4 py-6 z-50 gap-x-2 lg:hidden">
				<button class="bg-white rounded w-10 h-10 ring-1 ring-inset ring-gray-300 flex items-center justify-center"
						type="button">
					<mobile-filter-burger-icon></mobile-filter-burger-icon>
				</button>
				<div class="relative rounded-md shadow-sm flex-1">
					<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
						<mobile-filter-scope-icon></mobile-filter-scope-icon>
					</div>
					<input
						id="search-point"
						class="block w-full rounded-md border-0 py-1.5 h-10 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
						name="text"
						placeholder="Exemplu căutare"
						type="email"
					/>
				</div>
				<button
					class="bg-white rounded w-10 h-10 ring-1 ring-inset ring-gray-300 flex items-center justify-center"
					type="button"
				>
					<mobile-filter-svg-icon></mobile-filter-svg-icon>
				</button>
			</div>

			<top-menu
				:userInfo="userInfo"
				:is-authenticated="isAuthenticated"
			>

			</top-menu>

			<div
				class="map h-screen w-full bg-green-900"
			>
				<l-map
					ref="map"
					:center="[latitude, longitude]"
					:zoom="zoom"
					@ready="init"
					:use-global-leaflet="false"
				>
					<l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"></l-tile-layer>
					<template v-for="(point) of points">
						<l-marker
							:lat-lng="[point.lat, point.lon]"
							@click="markerClicked(point.id)"
						>
							<l-icon
								:icon-size="dynamicSize"
								icon-url="/assets/images/logo.png" >
							</l-icon>
						</l-marker>
					</template>

				</l-map>
			</div>
			<div
				class="grid grid-cols-2 absolute w-full z-50 bottom-0 bg-gray-500 px-3 py-2 text-white"
				:class="{'hidden': hasApprovedLocation}"
			>
				<div>{{CONSTANTS.LABELS.LOCATION.NOTICE}}</div>
				<div class="text-end">
					<a v-on:click="requestCurrentLocation()" class="cursor-pointer font-bold">{{CONSTANTS.LABELS.LOCATION.SETTINGS}}</a>
				</div>
			</div>
		</main>
	</div>
</template>

<script>
import LeftSidebar from "@/components/leftSidebar.vue";
import TopMenu from "@/components/topMenu.vue";
import L from 'leaflet';
import {LMap, LTileLayer, LControlLayers, LMarker, LIcon} from "@vue-leaflet/vue-leaflet";
import "leaflet/dist/leaflet.css"
import {CONSTANTS} from "../constants.js";
import MobileFilterSvgIcon from "./svg-icons/mobileFilterSvgIcon.vue";
import MobileFilterBurgerIcon from "./svg-icons/mobileFilterBurgerIcon.vue";
import MobileFilterScopeIcon from "./svg-icons/mobileFilterScopeIcon.vue";
import eventBus from "../eventBus.js";
import _ from "lodash";
import axios, {HttpStatusCode} from "axios";

export default
{
	components:
	{
		MobileFilterScopeIcon,
		MobileFilterBurgerIcon,
		MobileFilterSvgIcon,
		LIcon,
		LMarker,
		TopMenu,
		LeftSidebar,
		LMap,
		LTileLayer,
		LControlLayers
	},
	data ()
	{
		return {
			userInfo: {},
			isAuthenticated: false,
			open: false,
			zoom: 13,
			latitude: CONSTANTS.DEFAULT_LOCATION.LATITUDE,
			longitude: CONSTANTS.DEFAULT_LOCATION.LONGITUDE,
			points: {},
			icon: L.icon({
				iconUrl: '/assets/images/punct_benzinarie.svg',
				iconSize: [100, 37],
				iconAnchor: [16, 37]
			}),
			iconSize: 100,
			hasApprovedLocation: false

		};
	},
	methods:
	{
		markerClicked(id)
		{
			console.log(`clicked on marker`, id);
		},
		requestCurrentLocation()
		{
			const success = (position) =>
			{
				const latitude  = position.coords.latitude;
				const longitude = position.coords.longitude;

				this.latitude = latitude;
				this.longitude = longitude;

				this.hasApprovedLocation = true;
			};

			const error = (err) =>
			{
				console.log(error)
			};
			navigator.geolocation.getCurrentPosition(success, error);
		},
		getUserInfo ()
		{
			const session = localStorage.getItem('userSession');
			if (session)
			{
				axios
					.get(CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.USER.PROFILE.INFO)
					.then((response) =>
					{
						this.userInfo = _.get(response, 'data.data', {});
						this.isAuthenticated = true;
					}).catch((err) =>
				{
					this.isAuthenticated = false
				});
			}
			else
			{
				this.isAuthenticated = false;
			}
		},
		getMapPoints()
		{
			axios
				.get(CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.MAP.POINTS.INFO)
				.then((response) =>
				{
					this.points = _.get(response, 'data.points', {});
				}).catch((err) =>
			{
				console.log(err);
			});
		}
	},
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS
		},
		dynamicSize ()
		{
			return [this.iconSize, this.iconSize / 2.15];
		}
	},
	mounted()
	{
		this.requestCurrentLocation();

		eventBus.$on('getUser', (speaker) =>
		{
			this.getUserInfo();
		});
		this.getUserInfo();
		this.getMapPoints();
	}
};
</script>
