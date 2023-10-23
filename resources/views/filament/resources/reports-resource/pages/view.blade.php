@php
	$records = $getRecords();
@endphp
@if(count($records)>0)
	<section wire:ignore class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
		<table  class="fi-ta-table" style="table-layout:fixed; width: 100%;">
			<thead class="bg-gray-50 dark:bg-white/5">
				<tr>
					<th class="fi-ta-header-cell px-3 py-3.5">ID Punct</th>
					<th class="fi-ta-header-cell px-3 py-3.5">Tip locatie</th>
					<th class="fi-ta-header-cell px-3 py-3.5">Administrat de</th>
					<th class="fi-ta-header-cell px-3 py-3.5">Materiale</th>
					<th class="fi-ta-header-cell px-3 py-3.5">Judet</th>
					<th class="fi-ta-header-cell px-3 py-3.5">Oras</th>
					<th class="fi-ta-header-cell px-3 py-3.5">Grup</th>
					<th class="fi-ta-header-cell px-3 py-3.5">Status</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200 dark:divide-white/5">
				@if($this->isGrouped)
				@else
					@foreach($getRecords() as $record)
						<tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
							<td width="10px" class="fi-ta-cell p-0">{{ $record->id }}</td>
							<td class="fi-ta-cell p-0">{{ $record->type->display_name }}</td>
							<td class="fi-ta-cell p-0">{{ $record->managed_by }}</td>
							<td class="fi-ta-cell p-0">{!! $record->materials_icon !!}</td>
							<td class="fi-ta-cell p-0">{{ $record->county }}</td>
							<td class="fi-ta-cell p-0">{{ $record->city }}</td>

							<td class="fi-ta-cell p-0">{{ ($record->group)?$record->group->name:'' }}</td>
							<td class="fi-ta-cell p-0">{!! $record->status_badge !!}</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
		{{ $records->render() }}
	</section>
@endif