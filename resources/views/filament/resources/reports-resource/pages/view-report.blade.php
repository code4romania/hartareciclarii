<x-filament-panels::page
@class([
	'fi-resource-view-record-page',
	'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),

	])
	>
	@php
	$record = $this->getRecord();
	$header = $record->results['header'];
	$results = $record->results['results'];
	$filters = $record->form_data;
	@endphp
	<div class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
		<div class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
			<table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
				<thead class="bg-gray-50 dark:bg-white/5">
					<tr>
						@foreach($header as $column)
						<th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-id">
							<span class="text-sm font-semibold text-gray-950 dark:text-white">
								{{ $column }}
							</span>
						</th>
						@endforeach
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
					<tr>
						@foreach($results as $result)
						<td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-id">
							<div class="fi-ta-text grid gap-y-1 px-3 py-4">
								<div class="">
									<div class="flex max-w-max">
										<div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white  " style="">
											<div>
												{{ $result }}
											</div>
										</div>
									</div>
								</div>
							</div>
						</td>
						@endforeach
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</x-filament-panels::page>