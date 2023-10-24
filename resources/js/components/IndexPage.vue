<template>
	<left-sidebar>

	</left-sidebar>

	<div
		:class="{'g:pl-[4.5rem]': !open, 'lg:pl-72': open}"
	>  <!-- Toggle lg:pl-[4.5rem] OR lg:pl-72 -->
		<main class="">
			<div class="flex absolute inset-x-0 px-4 py-6 z-50 gap-x-2 lg:hidden">
				<button class="bg-white rounded w-10 h-10 ring-1 ring-inset ring-gray-300 flex items-center justify-center"
						type="button">
					<svg fill="none" height="12" viewBox="0 0 18 12" width="18" xmlns="http://www.w3.org/2000/svg">
						<path d="M0 12H18V10H0V12ZM0 7H18V5H0V7ZM0 0V2H18V0H0Z" fill="black"/>
					</svg>
				</button>
				<div class="relative rounded-md shadow-sm flex-1">
					<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
						<svg fill="none" height="20" viewBox="0 0 20 20" width="20"
							 xmlns="http://www.w3.org/2000/svg">
							<path clip-rule="evenodd" d="M8 4C5.79086 4 4 5.79086 4 8C4 10.2091 5.79086 12 8 12C10.2091 12 12 10.2091 12 8C12 5.79086 10.2091 4 8 4ZM2 8C2 4.68629 4.68629 2 8 2C11.3137 2 14 4.68629 14 8C14 9.29583 13.5892 10.4957 12.8907 11.4765L17.7071 16.2929C18.0976 16.6834 18.0976 17.3166 17.7071 17.7071C17.3166 18.0976 16.6834 18.0976 16.2929 17.7071L11.4765 12.8907C10.4957 13.5892 9.29583 14 8 14C4.68629 14 2 11.3137 2 8Z"
								  fill="#9CA3AF"
								  fill-rule="evenodd"></path>
						</svg>
					</div>
					<input id="search-point" class="block w-full rounded-md border-0 py-1.5 h-10 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" name="text"
						   placeholder="Exemplu căutare"
						   type="email">
				</div>
				<button class="bg-white rounded w-10 h-10 ring-1 ring-inset ring-gray-300 flex items-center justify-center"
						type="button">
					<svg fill="none" height="20" viewBox="0 0 18 20" width="18" xmlns="http://www.w3.org/2000/svg">
						<path clip-rule="evenodd" d="M0.600098 1.6C0.600098 0.93726 1.13736 0.400002 1.8001 0.400002H16.2001C16.8628 0.400002 17.4001 0.93726 17.4001 1.6V5.2C17.4001 5.51826 17.2737 5.82349 17.0486 6.04853L11.4001 11.6971V16C11.4001 16.3183 11.2737 16.6235 11.0486 16.8485L8.64863 19.2485C8.30543 19.5917 7.78929 19.6944 7.34088 19.5087C6.89247 19.3229 6.6001 18.8854 6.6001 18.4V11.6971L0.951569 6.04853C0.726526 5.82349 0.600098 5.51826 0.600098 5.2V1.6Z"
							  fill="#111827"
							  fill-rule="evenodd"/>
					</svg>
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
					<l-marker
						:lat-lng="markerLatLng"
						@click="markerClicked(123)"
					>
						<l-icon
							:icon-size="dynamicSize"
							icon-url="/assets/images/logo.png" >
						</l-icon>
					</l-marker>
				</l-map>
			</div>
			<div
				class="grid grid-cols-2 absolute w-full z-50 bottom-0 bg-green-800 px-3 py-2 text-white"
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

export default
{
	components:
	{
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
			latitude: 46.755504,
			longitude: 23.5787266,
			markerLatLng: [46.755504, 23.5787266],
			icon: L.icon({
				iconUrl: '/assets/images/logo.png',
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

				this.markerLatLng = [latitude, longitude];
				this.hasApprovedLocation = true;
			};

			const error = (err) =>
			{
				console.log(error)
			};
			navigator.geolocation.getCurrentPosition(success, error);
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
	}
};
</script>
