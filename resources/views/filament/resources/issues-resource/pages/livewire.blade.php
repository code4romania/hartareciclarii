<x-filament-panels::page
@class([
	'fi-resource-view-record-page',
	'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),

	])
	>
	@php
    $record = $this->record;
    $color = 'warning';
    $badgeLabel = trans('map_points.requires_verification');
    switch($record->status){
    	case '0':
    	$color = 'info';
    	break;
    	case '1':
    	$color = 'success';
    	break;
    	case '2':
    	$color = 'warning';
    	break;
    	case '3':
    	$color = 'danger';
    	break;
    }
    $badgeLabel = __('issues.status.'.$record->status);


@endphp




<x-filament::badge color="{{ $color }}">
    {{ $badgeLabel }}
</x-filament::badge>
	@livewire('issues.issue-type-'.$this->getRecord()->reported_point_issue_type_id,['record'=>$this->getRecord()])
</x-filament-panels::page>