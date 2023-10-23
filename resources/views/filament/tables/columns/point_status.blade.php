<div>
	@php
		$record = $getRecord();
		$status = $getState();
		if($record->issues->count()>0){
			$status = -1;
		}
	@endphp
	{{ $status }}

</div>