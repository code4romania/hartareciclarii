
     <style type="text/css">
     	#recycle-point-view-on-map { min-height: 320px; z-index: 1; }
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
@endphp






    	<div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
    		 <div  wire:ignore id="recycle-point-view-on-map"></div>
    	</div>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		var map = L.map('recycle-point-view-on-map').setView([{{ $record->location->latitude }}, {{ $record->location->longitude }}], 10);
		setInterval(function() {
		     map.invalidateSize();
		  }, 200);
		var marker = new L.Marker([{{ $record->location->latitude }}, {{ $record->location->longitude }}]);
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
