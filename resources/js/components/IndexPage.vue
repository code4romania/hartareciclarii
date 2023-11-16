<template>
	<div class="flex w-screen h-screen">
		<div
			class="flex"
			:class="{'g:w-[4.5rem]': !open, 'lg:w-96': open}"
		>
			<left-sidebar
				:has-results="hasResults"
				:total-points="totalPoints"
				@filtersChanged="setFilters($event)"
			></left-sidebar>

		</div>
		<div class="flex w-full h-full">
			<div
				:class="{'g:w-[4.5rem]': !open, 'lg:w-96': open}"
				class="h-screen w-full "
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

					<point-details
						v-if="Object.keys(selectedPoint).length > 0"
						:point="selectedPoint"
						:main-materials="mainMaterials"
						:point-url="pointUrl"
						:user-info="userInfo"
						@closePointDetails="closePointDetails($event)"
					>
					</point-details>
					<div :id="mapId" class="w-screen h-screen"></div>
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
		</div>
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
import PointDetails from "./pointDetails.vue";
import {getUserProfile} from "../general.js";
import { LMarkerClusterGroup } from 'vue-leaflet-markercluster'
import 'vue-leaflet-markercluster/dist/style.css'
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
		LControlLayers,
		LMarkerClusterGroup
    },
	data ()
	{
		return {
			userInfo: {},
			isAuthenticated: false,
			mapOptions: {
				zoom: CONSTANTS.DEFAULT_MAP_OPTIONS.ZOOM,
				maxZoom: CONSTANTS.DEFAULT_MAP_OPTIONS.MAX_ZOOM,
				lat: CONSTANTS.DEFAULT_MAP_OPTIONS.LATITUDE,
				lng: CONSTANTS.DEFAULT_MAP_OPTIONS.LONGITUDE,
				geoJSON: CONSTANTS.DEFAULT_MAP_OPTIONS.GEO_JSON,
			},
			open: false,
			points: {},
			mapId: 'leaflet-map',
			hasApprovedLocation: false,
			hasResults: true,
			totalPoints: 0,
			filters: {},
			bounds: {},
			selectedPoint: {},
			mainMaterials: {},
			pointUrl: '',
			mapInstance: {}

		};
	},
	methods:
	{
		getPoint(id)
		{
			let url = _.replace(CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.MAP.POINTS.DETAILS, '{id}', id);

			axios
				.get(url)
				.then((response) =>
				{
					this.selectedPoint = _.get(response, 'data.point', {});
					this.mainMaterials = _.get(response, 'data.materials', {});
					this.pointUrl = _.get(response, 'data.url', {});
				}).catch((err) =>
				{
					console.log(err);
				});
		},
		setBounds()
		{
			const leafletMap = L.map(this.mapId, {
				center: L.latLng(this.mapOptions.lat, this.mapOptions.lng),
				zoom: this.mapOptions.zoom,
				maxZoom: this.mapOptions.maxZoom,
				zoomControl: true,
				zoomAnimation: false,
				layers: [],
			});

			const ref = this;
			leafletMap.on('moveend', function(e)
			{
				ref.mapMoveEvent(e);
			});
			this.mapInstance = leafletMap;

			this.bounds = {
				northEast: {
					lat: leafletMap.getBounds().getNorthEast().lat,
					lng: leafletMap.getBounds().getNorthEast().lng,
				},
				northWest: {
					lat: leafletMap.getBounds().getNorthWest().lat,
					lng: leafletMap.getBounds().getNorthWest().lng,
				},
				southWest: {
					lat: leafletMap.getBounds().getSouthWest().lat,
					lng: leafletMap.getBounds().getSouthWest().lng,
				},
				southEast: {
					lat: leafletMap.getBounds().getSouthEast().lat,
					lng: leafletMap.getBounds().getSouthEast().lng,
				},
			};
		},
		async mapMoveEvent(e)
		{
			const leafletMap = this.mapInstance;
			this.bounds = {
				northEast: {
					lat: leafletMap.getBounds().getNorthEast().lat,
					lng: leafletMap.getBounds().getNorthEast().lng,
				},
				northWest: {
					lat: leafletMap.getBounds().getNorthWest().lat,
					lng: leafletMap.getBounds().getNorthWest().lng,
				},
				southWest: {
					lat: leafletMap.getBounds().getSouthWest().lat,
					lng: leafletMap.getBounds().getSouthWest().lng,
				},
				southEast: {
					lat: leafletMap.getBounds().getSouthEast().lat,
					lng: leafletMap.getBounds().getSouthEast().lng,
				},
			};

			this.mapOptions.lat = leafletMap.getCenter().lat;
			this.mapOptions.lng = leafletMap.getCenter().lng;
			this.mapOptions.geoJSON.geometry.coordinates = [leafletMap.getCenter().lng, leafletMap.getCenter().lat];
			this.mapOptions.zoom = leafletMap.getZoom();


			this.points = await this.getMapPoints();
			this.initMap(true);
		},
		requestCurrentLocation()
		{
			const success = async (position) =>
			{
				const latitude = position.coords.latitude;
				const longitude = position.coords.longitude;

				this.mapOptions.lat = latitude;
				this.mapOptions.lng = longitude;
				this.setBounds();
				this.hasApprovedLocation = true;


				this.points = await this.getMapPoints();
				this.initMap();
			};

			const error = async (err) =>
			{
				console.log(error);
				this.setBounds();
				this.points = await this.getMapPoints();
				this.initMap();
			};
			navigator.geolocation.getCurrentPosition(success, error);
		},
		async getUserInfo ()
		{
			console.log(`start get profile`);
			this.userInfo = await getUserProfile();
			if (Object.keys(this.userInfo).length > 0)
			{
				this.isAuthenticated = true;
			}
		},
		async getMapPoints(filters = {})
		{
			let points = {};
			await
				axios
				.get(CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.MAP.POINTS.INFO, {
					params: {
						bounds: this.bounds,
						filters: this.filters,
						lat: this.mapOptions.lat,
						lng: this.mapOptions.lng
					}
				})
				.then((response) =>
				{
					points = _.get(response, 'data.points', {});
					if (Object.keys(points).length > 0)
					{
						this.hasResults = true;
						this.totalPoints = Object.keys(this.points).length;
					}
					else
					{
						this.totalPoints = 0;
					}

					if ('search_key' in this.filters && this.filters.search_key.length > 3 && Object.keys(points).length === 0)
					{
						this.hasResults = false;
					}

				}).catch((err) =>
			{
				console.log(err);
			});

			return points;
		},
		initMap(refresh = false)
		{
			var leafletMap = this.mapInstance;
			if (refresh)
			{
				this.mapInstance.off();
				this.mapInstance.remove();
				this.mapInstance = {};
				leafletMap = L.map(this.mapId, {
					center: L.latLng(this.mapOptions.lat, this.mapOptions.lng),
					zoom: this.mapOptions.zoom,
					maxZoom: this.mapOptions.maxZoom,
					zoomControl: true,
					zoomAnimation: false,
					layers: [],
				});

				const ref = this;
				leafletMap.on('moveend', function(e)
				{
					ref.mapMoveEvent(e);
				});
				this.mapInstance = leafletMap;
			}


			const tile = L.tileLayer(
				`https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png`,
				{
					attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
				}
			).addTo(leafletMap);
			this.layerControlInstance = L.control.layers({OpenStreetMap: tile}).addTo(leafletMap);

			var markers = L.markerClusterGroup({
				maxClusterRadius: 120,
				spiderfyOnMaxZoom: false,
				showCoverageOnHover: false,
				iconCreateFunction: function (cluster) {

					var iconSettings = {
						mapIconUrl: '<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
							'<g id="Pin-multiple">\n' +
							'<path id="Ellipse 2" d="M39.283 20C39.283 30.6499 30.6496 39.2833 19.9998 39.2833C9.35004 39.2833 0.716667 30.6499 0.716667 20C0.716667 9.3501 9.35004 0.716667 19.9998 0.716667C30.6496 0.716667 39.283 9.3501 39.283 20Z" stroke="#00AD4D" stroke-width="1.43333"/>\n' +
							'<path id="Ellipse 5" d="M11.697 6C11.697 8.8952 9.26208 11.2833 6.20685 11.2833C3.15161 11.2833 0.716667 8.8952 0.716667 6C0.716667 3.1048 3.15161 0.716667 6.20685 0.716667C9.26208 0.716667 11.697 3.1048 11.697 6Z" fill="#004667" stroke="white" stroke-width="1.43333"/>\n' +
							'<path id="Ellipse 6" d="M39.2839 6C39.2839 8.8952 36.849 11.2833 33.7938 11.2833C30.7385 11.2833 28.3036 8.8952 28.3036 6C28.3036 3.1048 30.7385 0.716667 33.7938 0.716667C36.849 0.716667 39.2839 3.1048 39.2839 6Z" fill="#004667" stroke="white" stroke-width="1.43333"/>\n' +
							'<path id="Ellipse 7" d="M39.2839 31.3335C39.2839 34.2287 36.849 36.6168 33.7938 36.6168C30.7385 36.6168 28.3036 34.2287 28.3036 31.3335C28.3036 28.4383 30.7385 26.0502 33.7938 26.0502C36.849 26.0502 39.2839 28.4383 39.2839 31.3335Z" fill="#004667" stroke="white" stroke-width="1.43333"/>\n' +
							'<path id="Ellipse 8" d="M11.697 31.3335C11.697 34.2287 9.26208 36.6168 6.20685 36.6168C3.15161 36.6168 0.716667 34.2287 0.716667 31.3335C0.716667 28.4383 3.15161 26.0502 6.20685 26.0502C9.26208 26.0502 11.697 28.4383 11.697 31.3335Z" fill="#004667" stroke="white" stroke-width="1.43333"/>\n' +
							'</g>\n' +
							'</svg>\n',

						pinInnerCircleRadius: 48
					};

					return L.divIcon({
						html: L.Util.template(iconSettings.mapIconUrl, iconSettings),
						className: 'mycluster',
						iconSize: L.point(40, 40)
					});
				},
			});
			if (Object.keys(this.points).length > 0)
			{
				markers = this.addPointsToMap(markers);
			}

			leafletMap.addLayer(markers);

			this.mapInstance = leafletMap;
		},
		addPointsToMap(markers)
		{
			var ref = this;
			for (const point of this.points)
			{
				var icon = L.icon(
					{
						iconUrl: point?.icon || CONSTANTS.DEFAULT_MAP_OPTIONS.MARKER_ICON,
						iconSize: [50, 66.5],
						id: `marker_${point.id}`
					});

				let marker = L.marker(L.latLng(point.lat, point.lon), {
					id: `marker_${point.id}`,
					icon: icon
				}).on('click', function(e)
				{

					ref.getPoint(point.id);
				});

				markers.addLayer(marker)
			}

			return markers;
		},
		async setFilters (event)
		{
			if (Object.keys(event).length > 0)
			{
				this.filters = event;
				this.points = await this.getMapPoints();
				this.initMap(true);
			}

			if (this.hasResults === false)
			{
				this.filters = event;
				this.points = await this.getMapPoints();
				this.initMap(true);
			}
		},
		closePointDetails()
		{
			this.selectedPoint = {};
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
	},
};
</script>
