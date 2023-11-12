<template>
	<div class="flex w-screen h-screen">
		<main class="">

			<div :id="mapId" class="w-screen h-screen"></div>
		</main>
		<point-details
			v-if="Object.keys(mapPoint).length > 0"
			:point="mapPoint"
			:main-materials="mainMaterials"
			:point-url="pointUrl"
			:user-info="userInfo"
			@closePointDetails="closePointDetails($event)"
		>
		</point-details>
	</div>

</template>

<script>
import {CONSTANTS} from "../constants.js";
import _ from "lodash";
import axios, {HttpStatusCode} from "axios";
import L from 'leaflet';
import PointDetails from "./pointDetails.vue";

export default {
	components:
		{PointDetails},
	data ()
	{
		return {
			mapPoint: {},
			mainMaterials: {},
			pointUrl: '',
			userInfo: {},
			mapId: 'leaflet-map',
			mapOptions: {},
			mapInstance: null,
			layerControlInstance: null,
		};
	},
	methods:
		{
			async getPoint ()
			{
				let url = _.replace(CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.MAP.POINTS.DETAILS, '{id}', this.$route.params.point_id);
				let point = {};

				await axios
					.get(url)
					.then((response) =>
					{
						point = _.get(response, 'data', {});
					}).catch((err) =>
					{

					});

				return point;
			},
			initMap ()
			{
				const leafletMap = L.map(this.mapId, this.mapOptions);
				const tile = L.tileLayer(
					`https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png`,
					{
						attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
					}
				).addTo(leafletMap);
				this.layerControlInstance = L.control.layers({OpenStreetMap: tile}).addTo(leafletMap);

				let icon = L.icon(
		{
					iconUrl: '/assets/images/pin_selected.png',
				});

				let marker = L.marker([this.mapPoint.lat, this.mapPoint.lon], {
					id: `marker_${this.mapPoint.id}`,
					icon: icon
				});
				marker.addTo(leafletMap);
				this.mapInstance = leafletMap;
			},
			closePointDetails()
			{
				this.$router.push('/');
			}
		},
	async mounted ()
	{
		let point = await this.getPoint();
		this.mapPoint = _.get(point, 'point', {});
		this.mainMaterials = _.get(point, 'materials', {});
		this.pointUrl = _.get(point, 'url', {});

		this.mapOptions = {
			center: L.latLng(this.mapPoint.lat, this.mapPoint.lon),
			zoom: 13,
			zoomControl: true,
			zoomAnimation: false,
			layers: [],
		};
		this.initMap();
		console.log(this.mapPoint);
	},
	destroyed ()
	{
		if (this.mapInstance)
		{
			this.mapInstance.remove();
		}
	},
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS;
		}
	}
};
</script>

<style scoped>
@import 'leaflet/dist/leaflet.css';
</style>
