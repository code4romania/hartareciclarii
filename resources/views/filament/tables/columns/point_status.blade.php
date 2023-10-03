<div>
	@php
		$record = $getRecord();
		$status = $getState();
		if($record->getIssues->count()>0){
			$status = -1;
		}
	@endphp
	{{ $status }}

</div>