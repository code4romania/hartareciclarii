<style type="text/css">
	#recycle-point-map { min-height: 360px; }
	.leaflet-control-geocoder-icon:before{

/*		content: "🎮";*/
content: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>');
    vertical-align: -0.2em;
    padding-left: 0.1em;

	}
</style>

@php
$record = $getRecord();
$latitude = $record ? $record->lat : config('services.maps.defaults.latitude');
$longitude = $record ? $record->lon : config('services.maps.defaults.longitude');
$reverse_url = config('services.nominatim.url').config('services.nominatim.reverse');
@endphp

<div x-data="{ state: $wire.$entangle('lat') }">
    <input type="hidden" id="input_lat" x-model="state" />
</div>
<div x-data="{ state: $wire.$entangle('lon') }">
    <input type="hidden" id="input_lon" x-model="state" />
</div>
<div x-data="{ state: $wire.$entangle('city') }">
    <input type="hidden" id="input_city" x-model="state" />
</div>
<div x-data="map" x-init="init(JSON.stringify({
			'latitude': {{ $latitude }},
			'longitude': {{ $longitude }},
			'reverse_url':'{{ $reverse_url }}'

		}))" wire:ignore id="recycle-point-map"></div>

<script>

</script>