/*
* @Author: Bogdan Bocioaca
* @Date:   2023-10-04 12:16:53
* @Last Modified by:   Bogdan Bocioaca
* @Last Modified time: 2023-12-13 08:57:39
*/
latitude = null;
longitude = null;
reverse_url = null;
nominatim_url = null;
map = null
marker = null;
event = new Event('input');
var typingTimer;
var doneTypingInterval = 1000
const latElem = document.getElementById("input_lat");
const lonElem = document.getElementById("input_lon");
const cityElem = document.getElementById("input_city");
var addressElem = document.getElementById("data.address");


if(typeof addressElem === "undefined" || !addressElem){
	var addressElem = document.getElementById("mountedActionsData.0.address");
}
addressElem.addEventListener("keyup", function(){
	clearTimeout(typingTimer);
  	typingTimer = setTimeout(reverseAddress, doneTypingInterval);

});
addressElem.addEventListener('keydown', function () {
  clearTimeout(typingTimer);
});

function reverseAddress(e) {
	if(addressElem.value.length > 3){
		let url = `${nominatim_url}/search?format=json&q=${addressElem.value}&addressdetails=1`;
		fetch(url)
		.then(response=>response.json())
		.then(function(json){
			if(json.length > 0 && json[0] !== "undefined"){
				console.log(json[0])
				let result = json[0];
				console.log(result)
				var r = {lat: parseFloat(result.lat), lon: parseFloat(result.lon)}

				latElem.value = result.lat
				lonElem.value = result.lon
				latElem.dispatchEvent(event);
				lonElem.dispatchEvent(event);
				addressElem.dispatchEvent(event);
				if (marker) {
					marker
					.setLatLng(r);
				} else {
					marker = L.marker(r)
					.bindPopup(addressElem.value)
					.addTo(map);
				}
				map.panTo(new L.LatLng(r.lat,r.lon))
			}


		})

	}

}

document.addEventListener('alpine:init', () => {
	Alpine.data('map', () => ({
		title: '',
		init(params) {
			try{
				if(typeof params === 'undefined'){
					return
				}
				obj = JSON.parse(params);
				initMap(obj)
			}catch(err){
			}
		},
	})),

	Alpine.data('initMyparams', () => ({
		init() {
			this.$nextTick(() => this.$dispatch('myparams', { title: 'Initialised' }))
		},
	}))
})


function initMap(params){
	latitude = params.latitude;
	longitude = params.longitude
	reverse_url = params.reverse_url
	nominatim_url = params.nominatim_url
	map = L.map('recycle-point-map').setView([latitude,longitude], 16);
	setInterval(function() {
		map.invalidateSize();
	}, 200);
	marker = new L.Marker([latitude, longitude],{
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
				console.log(r.center)
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
	var addressElem = document.getElementById("data.address");
	if(typeof addressElem === "undefined" || !addressElem){
		var addressElem = document.getElementById("mountedActionsData.0.address");
	}
	latElem.value = latitude
	lonElem.value = longitude
	latElem.dispatchEvent(event);
	lonElem.dispatchEvent(event);
	let url = reverse_url;
	url =  url.replace('{latitude}',latitude);
	url =  url.replace('{longitude}',longitude);
	fetch(url)
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

	// document.addEventListener("DOMContentLoaded", () => {
	// 	getLocation()
	// })