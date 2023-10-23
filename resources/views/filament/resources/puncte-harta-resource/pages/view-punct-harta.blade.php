
<x-filament-panels::page
@class([
	'fi-resource-view-record-page',
	'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
	'fi-resource-record-' . $record->getKey(),
	])
	>
     <style type="text/css">
     	#recycle-point-view-on-map { min-height: 360px; z-index: 1; }
     	.edit-map-point{
     		margin: 0 auto;
			  display: grid;
			  gap: 1rem;

			  grid-auto-rows: minmax(25pc, auto);
     	}
     	@media (min-width: 600px) {
		  .edit-map-point { grid-template-columns: repeat(2, 1fr); }
		}
		@media (min-width: 900px) {
		  .edit-map-point {
		  	grid-template-columns: 1fr 3fr;
		  }
		}
		.material-tags{
			display: flex;flex-direction: row; display: flex;flex-direction: row;flex-wrap: wrap;
		}
		.material-tags div{
			margin-left:5px;
		}
     </style>

@php
    $record = $this->getRecord();
    $color = 'warning';
    $badgeLabel = trans('map_points.requires_verification');
    if ($record->issues->count())
    {
        $color = 'danger';
    }
    elseif ($record->status == 1)
    {
        $color = 'success';
    }
    if ($record->issues->count() > 0)
    {
        $badgeLabel = __('map_points.issues_found');
    }
    elseif ($record->status == 1)
    {
        $badgeLabel = __('map_points.verified');
    }


@endphp




<x-filament::badge color="{{ $color }}">
    {{ $badgeLabel }}
</x-filament::badge>


    <div class="edit-map-point grid mb-8  md:mb-12 md:grid-cols-3" style="">
    	<div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
			<h5 class=" flex items-center text-xl font-medium text-gray-900 dark:text-white">Localizare
				{{ $this->editLocationAction }}
    			<x-filament-actions::modals />
			</h5>
			<div class="flex item-center">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
			  		<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
			  		<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
				</svg>
				Adresa
			</div>
			<small>{{ $record->address }}</small>
			<br /><br />
			<div class="flex item-center">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
			  		<path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
				</svg>
				Coordonate
			</div>

			<small class="mb-5">{{ $record->lat }}, {{ $record->lon }}</small>
			<br /><br />
			<div class="flex item-center">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
			  		<path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
				</svg>
				Notite localizare (private)
			</div>
			<small class="mb-5">{{ $record->location_notes}}</small>
			<br /><br />
    	</div>
    	<div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    		 <div  wire:ignore id="recycle-point-view-on-map"></div>
    	</div>
    	<div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
			<h5 class=" flex items-center text-xl font-medium text-gray-900 dark:text-white" style="justify-content: space-between;">Detalii punct
				{{ $this->editDetailsAction }}
    			<x-filament-actions::modals />
			</h5>
		    <form class="space-y-6" action="#">
		        <div>
		            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tip punct</label>
		            <input value="{{ $record->type->display_name }}" type="email" disabled="true" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
		        </div>
		        <div>
		            <label for="materials" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Materiale colectate</label>
		            <div class="material-tags" style="">
						@foreach($record->materials as $material)
			            	<x-filament::badge>
	                            {{ $material->name }}

	                        </x-filament::badge>
			            @endforeach
		            </div>

		        </div>
		        <div>
		            <label for="managed_by" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Administrat de:</label>
		            <input value="{{ $record->managed_by }}" type="text" disabled="true" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
		        </div>
		        <div>
		            <label for="website" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Website:</label>
		            <input value="{{ $record->website }}" type="text" disabled="true" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
		        </div>
		        <div>
		            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email:</label>
		            <input value="{{ $record->email }}" type="text" disabled="true" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
		        </div>
		        <div>
		            <label for="phone_no" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Telefon:</label>
		            <input value="{{ $record->phone_no }}" type="text" disabled="true" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
		        </div>
		        <div>
		            <label for="opening_hours" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Orar:</label>
		            @foreach($record->opening_hours as $day)
		            	@if($day['startDay'] == $day['endDay'])
		            		{{ __('common.week_days.'.$day['startDay']) }}:
		            	@else
		            		{{ __('common.week_days.'.$day['startDay']) }} - {{ __('common.week_days.'.$day['end_day']) }} <br />
		            	@endif
		            	@if($day['closed'])
		            		{{ __('common.closed') }}
		            	@else
		            		{{ $day['startHour'] }}-{{ $day['endHour'] }}
		            	@endif

		            	<br />
		            @endforeach

		        </div>
		        <div>
		            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alte detalii: </label>
		            <textarea class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" >{{ $record->details }}</textarea>
		        </div>
		        <div class="flex items-center mb-4">
				    <input id="default-checkbox" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" disabled="true" {{ $record->offers_transport?"checked":"" }}>
				    <label for="default-checkbox" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">&nbsp; Ofera transport</label>
				</div>
				<div class="flex items-center mb-4">
				    <input id="default-checkbox" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" disabled="true" {{ $record->offers_money?"checked":"" }}>
				    <label for="default-checkbox" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">&nbsp; Ofera bani</label>
				</div>


		    </form>
			<br /><br />
    	</div>
    	<div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    		<h5 class=" flex items-center text-xl font-medium text-gray-900 dark:text-white" style="justify-content: space-between;">Activitate punct</h5>
    		{{ $this->table }}
    	</div>

    </div>


</x-filament-panels::page>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		var map = L.map('recycle-point-view-on-map').setView([{{ $record->lat }}, {{ $record->lon }}], 16);
		setInterval(function() {
		     map.invalidateSize();
		  }, 200);
		var marker = new L.Marker([{{ $record->lat }}, {{ $record->lon }}]);
		marker.addTo(map);

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
    });

</script>