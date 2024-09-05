<style type="text/css">
    #recycle-point-map { min-height: 360px; z-index: 0}
</style>

{{json_encode($location)}}

<div x-data="map" wire:model="location" x-init="init(JSON.stringify({
			'latitude': {{ $location[0] }},
			'longitude': {{ $location[1] }},
			'nominatim_url': '{{ config('services.nominatim.url') }}'

		}))" wire:ignore id="recycle-point-map"></div>

