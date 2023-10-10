<x-filament-panels::page>
@if($this->view_type == 'processed')
	{{ $this->table }}
@elseif($this->view_type == 'failed')
	@php
		$record = $this->getRecord();
		$failed = $record->result['failed'];
	@endphp
	<div class="py-2 px-5">
		<table class="fi-ta-table" style="table-layout:fixed; width: 100%;">
			<thead class="bg-gray-50 dark:bg-white/5">
				<tr>
					<th class="fi-ta-header-cell px-3 py-3.5">Rand</th>
					<th class="fi-ta-header-cell px-3 py-3.5">Erori</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-200 dark:divide-white/5">
				@foreach($failed as $row => $errors)
				<tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
					<td width="10px" class="fi-ta-cell p-0">{{ $row }}</td>
					<td class="fi-ta-cell p-0">
						@foreach($errors as $err )
							@php
								$arrErr = explode("=>",$err);
								$args = [];
								if(isset($arrErr[1])){
									$values = explode("|",$arrErr[1]);
									$args[$values[0]] = $values[1];
								}


							@endphp
							{!! __('imports.'.$arrErr[0],$args) !!}<br />
						@endforeach
					</td>

				</tr>
				@endforeach
			</tbody>

		</table>

	</div>
@endif
</x-filament-panels::page>