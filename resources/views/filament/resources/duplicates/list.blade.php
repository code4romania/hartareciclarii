@php
	$record = $getRecord();
@endphp
<div class="py-2 px-5">
	<table class="fi-ta-table" style="table-layout:fixed; width: 100%;">
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
			<tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
				<td width="10px" class="fi-ta-cell p-0">{{ $record->recycle_point_1 }}</td>
				<td class="fi-ta-cell p-0">{{ $record->getType->display_name }}</td>
				<td class="fi-ta-cell p-0">{{ $record->firstPoint->managed_by }}</td>
				<td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-get-materials.get-parent.icon">{!! $record->firstPoint->materials_icon !!}</td>
				<td class="fi-ta-cell p-0">{{ $record->firstPoint->county }}</td>
				<td class="fi-ta-cell p-0">{{ $record->firstPoint->city }}</td>

				<td class="fi-ta-cell p-0">{{ $record->firstPoint->group }}</td>
				<td class="fi-ta-cell p-0">{!! $record->firstPoint->status_badge !!}</td>

			</tr>
			<tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
				<td class="fi-ta-cell p-0">{{ $record->recycle_point_2 }}</td>
				<td class="fi-ta-cell p-0">{{ $record->getType->display_name }}</td>
				<td class="fi-ta-cell p-0">{{ $record->secondPoint->managed_by }}</td>
				<td class="fi-ta-cell p-0">{!! $record->secondPoint->materials_icon !!}</td>
				<td class="fi-ta-cell p-0">{{ $record->secondPoint->county }}</td>
				<td class="fi-ta-cell p-0">{{ $record->secondPoint->city }}</td>

				<td class="fi-ta-cell p-0">{{ $record->secondPoint->group }}</td>
				<td class="fi-ta-cell p-0">{!! $record->secondPoint->status_badge !!}</td>

			</tr>
		</tbody>

	</table>

</div>
