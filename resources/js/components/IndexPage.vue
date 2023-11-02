<template>
	<left-sidebar
        :has-results="hasResults"
		@filtersChanged="setFilters($event)"
    ></left-sidebar>

	<div
		:class="{'g:pl-[4.5rem]': !open, 'lg:pl-96': open}"
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
					@ready="initMap"
					@update:zoom="zoomEvent($event)"
					@update:center="centerEvent($event)"
					@update:bounds="boundsEvent($event)"
					:use-global-leaflet="false"
				>
					<l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"></l-tile-layer>
					<template v-for="(point) of points">
						<l-marker
							:lat-lng="[point.lat, point.lon]"
							:ref="`marker_${point.id}`"
							@click="markerClicked(point.id, point.lat, point.lon)"
							:icon="getIcon(point.id)"
						>
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
	<point-details
		v-if="pointId > 0"
		:point_id="pointId"
	>
	</point-details>
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
import PointDetails from "./pointDetails.vue";

export default
{
	components:
	{
		PointDetails,
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
			mapIcon: {
				iconUrl: '/assets/images/pin.png',
				iconSize: [48, 64],
				iconAnchor: [48, 64],
				popupAnchor: [-3, -76]
			},
			mapIconSelected: {
				iconUrl: '/assets/images/pin_selected.png',
				iconSize: [56, 75],
				iconAnchor: [56, 75],
				popupAnchor: [-3, -76]
			},
			overlayingIcons:{
				colectare_separata_magazin: {
					iconUrl: '/assets/images/colectare_separata_magazin.png',
					iconSize: [48, 64],
					iconAnchor: [48, 64],
					popupAnchor: [-3, -76]
				}
			},
			hasApprovedLocation: false,
			hasResults: false,
			filters: {},
			bounds: {},
			pointId: 0,

		};
	},
	methods:
	{
		markerClicked(id, lat, lng)
		{
			this.latitude = lat;
			this.longitude = lng;
			this.pointId = id;
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
				this.getMapPoints();
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
		getMapPoints(filters = {})
		{
			axios
				.get(CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.MAP.POINTS.INFO, {
					params: {
						bounds: this.bounds,
						filters: this.filters,
						lat: this.latitude,
						lng: this.longitude
					}
				})
				.then((response) =>
				{
					this.points = _.get(response, 'data.points', {});
					console.log(this.points);
					if (Object.keys(this.points).length > 0)
					{
						this.hasResults = true;
					}
				}).catch((err) =>
			{
				console.log(err);
			});
		},
		initMap()
		{
			this.map = this.$refs.map.leafletObject;
			this.bounds = {
				northEast: {
					lat: this.map.getBounds().getNorthEast().lat,
					lng: this.map.getBounds().getNorthEast().lng,
				},
				northWest: {
					lat: this.map.getBounds().getNorthWest().lat,
					lng: this.map.getBounds().getNorthWest().lng,
				},
				southWest: {
					lat: this.map.getBounds().getSouthWest().lat,
					lng: this.map.getBounds().getSouthWest().lng,
				},
				southEast: {
					lat: this.map.getBounds().getSouthEast().lat,
					lng: this.map.getBounds().getSouthEast().lng,
				},
			};
			this.getMapPoints();
		},
		setFilters(event)
		{
			if (Object.keys(event).length > 0)
			{
				this.filters = event;
				this.getMapPoints();
			}
		},
		zoomEvent(event)
		{
			if (event != this.zoom)
			{
				this.getMapPoints();
			}

			this.zoom = event;
		},
		centerEvent(event)
		{
			this.latitude = event.lat;
			this.longitude = event.lng;

			this.getMapPoints();
		},
		boundsEvent(event)
		{
			this.bounds = {
				northEast: {
					lat: event.getNorthEast().lat,
					lng: event.getNorthEast().lng,
				},
				northWest: {
					lat: event.getNorthWest().lat,
					lng: event.getNorthWest().lng,
				},
				southWest: {
					lat: event.getSouthWest().lat,
					lng: event.getSouthWest().lng,
				},
				southEast: {
					lat: event.getSouthEast().lat,
					lng: event.getSouthEast().lng,
				}
			};
		},
		getIcon(point)
		{
			if (this.pointId === point)
			{
				return L.icon(this.mapIconSelected);
			}
			return L.icon(this.mapIcon);
		}
	},
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS
		},
		dynamicSize ()
		{
			return [this.iconSize, this.iconSize * 1.33];
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
	}
};
</script>
