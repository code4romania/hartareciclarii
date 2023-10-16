<style type="text/css">
	#recycle-point-map { min-height: 360px; }
	.leaflet-control-geocoder-icon:before{

/*		content: "🎮";*/
content: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>');
    vertical-align: -0.2em;
    padding-left: 0.1em;

	}
</style>

<div x-data="{ state: $wire.$entangle('lat') }">
    <input type="hidden" id="input_lat" x-model="state" />
</div>
<div x-data="{ state: $wire.$entangle('lon') }">
    <input type="hidden" id="input_lon" x-model="state" />
</div>
<div x-data="{ state: $wire.$entangle('city') }">
    <input type="hidden" id="input_city" x-model="state" />
</div>

<div wire:ignore id="recycle-point-map"></div>

<script>
	latitude = {{ config('services.maps.defaults.latitude') }};
	longitude = {{ config('services.maps.defaults.longitude') }};
	map = null
	event = new Event('input');
	function getLocation() {
		initMap();
	}
	function initMap(){
		map = L.map('recycle-point-map').setView([latitude, longitude], 16);
		setInterval(function() {
			map.invalidateSize();
		}, 200);
		var marker = new L.Marker([latitude, longitude],{
			draggable: true,
			autoPan: true
		});
		marker.addTo(map);
		marker.on("drag", function(e) {
			var marker = e.target;
			var position = marker.getLatLng();
			map.panTo(new L.LatLng(position.lat, position.lng));
		});
		marker.on('dragend', function(event) {
			var latlng = event.target.getLatLng();
			updateInputs(latlng.lat,latlng.lng)
		});

		var Stadia_AlidadeSmooth = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.{ext}', {
			minZoom: 0,
			maxZoom: 20,
			attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
			ext: 'png'
		});
		Stadia_AlidadeSmooth.addTo(map)
		map.on('zoomend', function() {
			var bounds = map.getBounds();
		});
		map.on('moveend', function() {
			var bounds = map.getBounds();
		});
		L.Control.geocoder().addTo(map);
		var geocoder = L.Control.Geocoder.nominatim();
		map.on('click', function(e) {
			geocoder.reverse(e.latlng, map.options.crs.scale(map.getZoom()), function(results) {
				var r = results[0];
				if (r) {

					if (marker) {
						marker
						.setLatLng(r.center);
					} else {
						marker = L.marker(r.center)
						.bindPopup(r.name)
						.addTo(map);
					}
					updateInputs(r.center.lat,r.center.lng)
					map.panTo(new L.LatLng(r.center.lat,r.center.lng))
				}

			});
		});
		updateInputs(latitude, longitude)
	}
	function onMapClick(e) {
		marker = new L.marker(e.latlng, {draggable:'true'});
		marker.on('dragend', function(event){
			var marker = event.target;
			var position = marker.getLatLng();
			marker.setLatLng(new L.LatLng(position.lat, position.lng),{draggable:'true'});
			map.panTo(new L.LatLng(position.lat, position.lng))
		});
		map.addLayer(marker);
	};


	function updateInputs(latitude, longitude){
		const latElem = document.getElementById("input_lat");
		const lonElem = document.getElementById("input_lon");
		const cityElem = document.getElementById("input_city");
		const addressElem = document.getElementById("data.address");
		latElem.value = latitude
		lonElem.value = longitude
		latElem.dispatchEvent(event);
		lonElem.dispatchEvent(event);
		fetch("https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat="+latitude+"&lon="+longitude)
		.then(response=>response.json())
		.then(function(json){

			if(typeof json.address !== "undefined"){
				cityElem.value = json.address.city;
				cityElem.dispatchEvent(event);
			}
			addressElem.value = json.display_name;
			addressElem.dispatchEvent(event);
		})
	}
	document.addEventListener("DOMContentLoaded", () => {
		getLocation()
	})
</script>