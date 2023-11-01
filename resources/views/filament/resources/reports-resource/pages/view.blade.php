@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Enums\ActionsPosition;
    use Filament\Tables\Enums\FiltersLayout;
    use Filament\Tables\Enums\RecordCheckboxPosition;

    $actions = $getActions();
    $actionsAlignment = $getActionsAlignment();
    $actionsPosition = $getActionsPosition();
    $actionsColumnLabel = $getActionsColumnLabel();
    $columns = $getVisibleColumns();
    $collapsibleColumnsLayout = $getCollapsibleColumnsLayout();
    $content = $getContent();
    $contentGrid = $getContentGrid();
    $contentFooter = $getContentFooter();
    $filterIndicators = [
        ...($hasSearch() ? ['resetTableSearch' => $getSearchIndicator()] : []),
        ...collect($getColumnSearchIndicators())
            ->mapWithKeys(fn (string $indicator, string $column): array => [
                "resetTableColumnSearch('{$column}')" => $indicator,
            ])
            ->all(),
        ...array_reduce(
            $getFilters(),
            fn (array $carry, \Filament\Tables\Filters\BaseFilter $filter): array => [
                ...$carry,
                ...collect($filter->getIndicators())
                    ->mapWithKeys(fn (string $label, int | string $field) => [
                        "removeTableFilter('{$filter->getName()}'" . (is_string($field) ? ' , \'' . $field . '\'' : null) . ')' => $label,
                    ])
                    ->all(),
            ],
            [],
        ),
    ];
    $hasColumnsLayout = $hasColumnsLayout();
    $hasSummary = $hasSummary();
    $header = $getHeader();
    $headerActions = array_filter(
        $getHeaderActions(),
        fn (\Filament\Tables\Actions\Action | \Filament\Tables\Actions\BulkAction | \Filament\Tables\Actions\ActionGroup $action): bool => $action->isVisible(),
    );
    $headerActionsPosition = $getHeaderActionsPosition();
    $heading = $getHeading();
    $group = $getGrouping();
    $bulkActions = array_filter(
        $getBulkActions(),
        fn (\Filament\Tables\Actions\BulkAction | \Filament\Tables\Actions\ActionGroup $action): bool => $action->isVisible(),
    );
    $groups = $getGroups();
    $description = $getDescription();
    $isGroupsOnly = $isGroupsOnly() && $group;
    $isReorderable = $isReorderable();
    $isReordering = $isReordering();
    $isColumnSearchVisible = $isSearchableByColumn();
    $isGlobalSearchVisible = $isSearchable();
    $isSelectionEnabled = $isSelectionEnabled();
    $recordCheckboxPosition = $getRecordCheckboxPosition();
    $isStriped = $isStriped();
    $isLoaded = $isLoaded();
    $hasFilters = $isFilterable();
    $filtersLayout = $getFiltersLayout();
    $filtersTriggerAction = $getFiltersTriggerAction();
    $hasFiltersDropdown = $hasFilters && ($filtersLayout === FiltersLayout::Dropdown);
    $hasFiltersAboveContent = $hasFilters && in_array($filtersLayout, [FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible]);
    $hasFiltersAboveContentCollapsible = $hasFilters && ($filtersLayout === FiltersLayout::AboveContentCollapsible);
    $hasFiltersBelowContent = $hasFilters && ($filtersLayout === FiltersLayout::BelowContent);
    $hasColumnToggleDropdown = $hasToggleableColumns();
    $hasHeader = $header || $heading || $description || ($headerActions && (! $isReordering)) || $isReorderable || count($groups) || $isGlobalSearchVisible || $hasFilters || count($filterIndicators) || $hasColumnToggleDropdown;
    $hasHeaderToolbar = $isReorderable || count($groups) || $isGlobalSearchVisible || $hasFiltersDropdown || $hasColumnToggleDropdown;
    $pluralModelLabel = $getPluralModelLabel();
    $records = $isLoaded ? $getRecords() : null;
    $allSelectableRecordsCount = ($isSelectionEnabled && $isLoaded) ? $getAllSelectableRecordsCount() : null;
    $columnsCount = count($columns);
    $reorderRecordsTriggerAction = $getReorderRecordsTriggerAction($isReordering);
    $toggleColumnsTriggerAction = $getToggleColumnsTriggerAction();

    if (count($actions) && (! $isReordering)) {
        $columnsCount++;
    }

    if ($isSelectionEnabled || $isReordering) {
        $columnsCount++;
    }

    if ($group) {
        $groupedSummarySelectedState = $this->getTableSummarySelectedState($this->getAllTableSummaryQuery(), modifyQueryUsing: fn (\Illuminate\Database\Query\Builder $query) => $group->groupQuery($query, model: $getQuery()->getModel()));
    }

    $getHiddenClasses = function (Filament\Tables\Columns\Column $column): ?string {
        if ($breakpoint = $column->getHiddenFrom()) {
            return match ($breakpoint) {
                'sm' => 'sm:hidden',
                'md' => 'md:hidden',
                'lg' => 'lg:hidden',
                'xl' => 'xl:hidden',
                '2xl' => '2xl:hidden',
            };
        }

        if ($breakpoint = $column->getVisibleFrom()) {
            return match ($breakpoint) {
                'sm' => 'hidden sm:table-cell',
                'md' => 'hidden md:table-cell',
                'lg' => 'hidden lg:table-cell',
                'xl' => 'hidden xl:table-cell',
                '2xl' => 'hidden 2xl:table-cell',
            };
        }

        return null;
    };
    $headerLabels = [];
	$total = 0;
	foreach($columns as $label => $column):
		$headerLabels[] = $label;
	endforeach;
@endphp

<div
    @if (! $isLoaded)
        wire:init="loadTable"
    @endif
    x-ignore
    ax-load
    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('table', 'filament/tables') }}"
    x-data="table"
    @class([
        'fi-ta',
        'animate-pulse' => $records === null,
    ])
>
    <x-filament-tables::container>
        <div

            class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/10"
        >
            @if ($header)
                {{ $header }}
            @elseif (($heading || $description || $headerActions) && ! $isReordering)
                <x-filament-tables::header
                    :actions="$isReordering ? [] : $headerActions"
                    :actions-position="$headerActionsPosition"
                    :description="$description"
                    :heading="$heading"
                />
            @endif

            @if ($hasFiltersAboveContent)
                <div
                    x-data="{ areFiltersOpen: @js(! $hasFiltersAboveContentCollapsible) }"
                    @class([
                        'grid px-4 sm:px-6',
                        'py-4' => ! $hasFiltersAboveContentCollapsible,
                        'gap-y-3 py-2.5 sm:gap-y-1 sm:py-3' => $hasFiltersAboveContentCollapsible,
                    ])
                >
                    @if ($hasFiltersAboveContentCollapsible)
                        <span
                            x-on:click="areFiltersOpen = ! areFiltersOpen"
                            @class([
                                'ms-auto inline-flex',
                                '-mx-2' => $filtersTriggerAction->isIconButton(),
                            ])
                        >
                            {{ $filtersTriggerAction->badge(count(\Illuminate\Support\Arr::flatten($filterIndicators))) }}
                        </span>
                    @endif

                    <x-filament-tables::filters
                        :form="$getFiltersForm()"
                        x-cloak
                        x-show="areFiltersOpen"
                        @class([
                            'py-1 sm:py-3' => $hasFiltersAboveContentCollapsible,
                        ])
                    />
                </div>
            @endif

            <div
                @if (! $hasHeaderToolbar) x-cloak @endif
                x-show="@js($hasHeaderToolbar) || (selectedRecords.length && @js(count($bulkActions)))"
                class="fi-ta-header-toolbar flex items-center justify-between gap-3 px-4 py-3 sm:px-6"
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.start', scopes: static::class) }}

                <div class="flex shrink-0 items-center gap-x-3">
                    {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.reorder-trigger.before', scopes: static::class) }}

                    @if ($isReorderable)
                        <span
                            x-show="! selectedRecords.length"
                            @class([
                                'inline-flex',
                                '-me-1 -ms-2' => $reorderRecordsTriggerAction->isIconButton(),
                            ])
                        >
                            {{ $reorderRecordsTriggerAction }}
                        </span>
                    @endif

                    {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.reorder-trigger.after', scopes: static::class) }}

                    @if ((! $isReordering) && count($bulkActions))
                        <x-filament-tables::actions
                            :actions="$bulkActions"
                            x-cloak="x-cloak"
                            x-show="selectedRecords.length"
                        />
                    @endif

                    {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.grouping-selector.before', scopes: static::class) }}

                    @if (count($groups))
                        <x-filament-tables::groups
                            :dropdown-on-desktop="$areGroupsInDropdownOnDesktop()"
                            :groups="$groups"
                            :trigger-action="$getGroupRecordsTriggerAction()"
                        />
                    @endif

                    {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.grouping-selector.after', scopes: static::class) }}
                </div>

                @if ($isGlobalSearchVisible || $hasFiltersDropdown || $hasColumnToggleDropdown)
                    <div
                        @class([
                            'ms-auto flex items-center',
                            'gap-x-3' => ! ($filtersTriggerAction->isIconButton() && $toggleColumnsTriggerAction->isIconButton()),
                            'gap-x-4' => $filtersTriggerAction->isIconButton() && $toggleColumnsTriggerAction->isIconButton(),
                        ])
                    >
                        {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.search.before', scopes: static::class) }}

                        @if ($isGlobalSearchVisible)
                            <x-filament-tables::search-field />
                        @endif

                        {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.search.after', scopes: static::class) }}

                        @if ($hasFiltersDropdown || $hasColumnToggleDropdown)
                            @if ($hasFiltersDropdown)
                                <x-filament-tables::filters.dropdown
                                    :form="$getFiltersForm()"
                                    :indicators-count="count(\Illuminate\Support\Arr::flatten($filterIndicators))"
                                    :max-height="$getFiltersFormMaxHeight()"
                                    :trigger-action="$filtersTriggerAction"
                                    :width="$getFiltersFormWidth()"
                                />
                            @endif

                            {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.toggle-column-trigger.before', scopes: static::class) }}

                            @if ($hasColumnToggleDropdown)
                                <x-filament-tables::column-toggle.dropdown
                                    :form="$getColumnToggleForm()"
                                    :max-height="$getColumnToggleFormMaxHeight()"
                                    :trigger-action="$toggleColumnsTriggerAction"
                                    :width="$getColumnToggleFormWidth()"
                                />
                            @endif

                            {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.toggle-column-trigger.after', scopes: static::class) }}
                        @endif
                    </div>
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.end') }}
            </div>
        </div>

        @if ($isReordering)
            <x-filament-tables::reorder.indicator :colspan="$columnsCount" />
        @elseif ($isSelectionEnabled && $isLoaded)
            <x-filament-tables::selection.indicator
                :all-selectable-records-count="$allSelectableRecordsCount"
                :colspan="$columnsCount"
                x-bind:hidden="! selectedRecords.length"
                x-show="selectedRecords.length"
            />
        @endif

        @if (count($filterIndicators))
            <x-filament-tables::filters.indicators
                :indicators="$filterIndicators"
            />
        @endif

        <div
            @if ($pollingInterval = $getPollingInterval())
                wire:poll.{{ $pollingInterval }}
            @endif
            @class([
                'fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10',
                '!border-t-0' => ! $hasHeader,
            ])
        >
            @if (($content || $hasColumnsLayout) && ($records !== null) && count($records))
                @if (! $isReordering)
                    @php
                        $sortableColumns = array_filter(
                            $columns,
                            fn (\Filament\Tables\Columns\Column $column): bool => $column->isSortable(),
                        );
                    @endphp

                    @if ($isSelectionEnabled || count($sortableColumns))
                        <div
                            class="flex items-center gap-4 gap-x-6 bg-gray-50 px-4 dark:bg-white/5 sm:px-6"
                        >
                            @if ($isSelectionEnabled && (! $isReordering))
                                <x-filament-tables::selection.checkbox
                                    :label="__('filament-tables::table.fields.bulk_select_page.label')"
                                    x-bind:checked="
                                        const recordsOnPage = getRecordsOnPage()

                                        if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                            $el.checked = true

                                            return 'checked'
                                        }

                                        $el.checked = false

                                        return null
                                    "
                                    x-on:click="toggleSelectRecordsOnPage"
                                    class="my-4"
                                />
                            @endif

                            @if (count($sortableColumns))
                                <div
                                    x-data="{
                                        column: $wire.$entangle('tableSortColumn', true),
                                        direction: $wire.$entangle('tableSortDirection', true),
                                    }"
                                    x-init="
                                        $watch('column', function (newColumn, oldColumn) {
                                            if (! newColumn) {
                                                direction = null

                                                return
                                            }

                                            if (oldColumn) {
                                                return
                                            }

                                            direction = 'asc'
                                        })
                                    "
                                    class="flex gap-x-3 py-3"
                                >
                                    <label>
                                        <x-filament::input.wrapper
                                            :prefix="__('filament-tables::table.sorting.fields.column.label')"
                                        >
                                            <x-filament::input.select
                                                x-model="column"
                                            >
                                                <option value="">-</option>

                                                @foreach ($sortableColumns as $column)
                                                    <option
                                                        value="{{ $column->getName() }}"
                                                    >
                                                        {{ $column->getLabel() }}
                                                    </option>
                                                @endforeach
                                            </x-filament::input.select>
                                        </x-filament::input.wrapper>
                                    </label>

                                    <label x-cloak x-show="column">
                                        <span class="sr-only">
                                            {{ __('filament-tables::table.sorting.fields.direction.label') }}
                                        </span>

                                        <x-filament::input.wrapper>
                                            <x-filament::input.select
                                                x-model="direction"
                                            >
                                                <option value="asc">
                                                    {{ __('filament-tables::table.sorting.fields.direction.options.asc') }}
                                                </option>

                                                <option value="desc">
                                                    {{ __('filament-tables::table.sorting.fields.direction.options.desc') }}
                                                </option>
                                            </x-filament::input.select>
                                        </x-filament::input.wrapper>
                                    </label>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif

                @if ($hasSummary && (! $isReordering))
                    <x-filament-tables::table>
                        <x-filament-tables::summary
                            :columns="$columns"
                            extra-heading-column
                            :placeholder-columns="false"
                            :plural-model-label="$pluralModelLabel"
                            :records="$records"
                        />
                    </x-filament-tables::table>
                @endif
            @elseif (($records !== null) && count($records))
                <x-filament-tables::table :reorderable="$isReorderable">
                    <x-slot name="header">
                        @if ($isReordering)
                            <th></th>
                        @else
                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                @if ($actionsColumnLabel)
                                    <x-filament-tables::header-cell>
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-1"></th>
                                @endif
                            @endif

                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                <x-filament-tables::selection.cell tag="th">
                                    <x-filament-tables::selection.checkbox
                                        :label="__('filament-tables::table.fields.bulk_select_page.label')"
                                        x-bind:checked="
                                            const recordsOnPage = getRecordsOnPage()

                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                $el.checked = true

                                                return 'checked'
                                            }

                                            $el.checked = false

                                            return null
                                        "
                                        x-on:click="toggleSelectRecordsOnPage"
                                    />
                                </x-filament-tables::selection.cell>
                            @endif

                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                @if ($actionsColumnLabel)
                                    <x-filament-tables::header-cell>
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-1"></th>
                                @endif
                            @endif
                        @endif

                        @foreach ($columns as $column)
                            <x-filament-tables::header-cell
                                :actively-sorted="$getSortColumn() === $column->getName()"
                                :alignment="$column->getAlignment()"
                                :name="$column->getName()"
                                :sortable="$column->isSortable() && (! $isReordering)"
                                :sort-direction="$getSortDirection()"
                                :wrap="$column->isHeaderWrapped()"
                                :attributes="
                                    \Filament\Support\prepare_inherited_attributes($column->getExtraHeaderAttributeBag())
                                        ->class([
                                            'fi-table-header-cell-' . str($column->getName())->camel()->kebab(),
                                            $getHiddenClasses($column),
                                        ])
                                "
                            >
                                {{ $column->getLabel() }}
                            </x-filament-tables::header-cell>
                        @endforeach

                        @if (! $isReordering)
                            @if (count($actions) && $actionsPosition === ActionsPosition::AfterColumns)
                                @if ($actionsColumnLabel)
                                    <x-filament-tables::header-cell
                                        :alignment="Alignment::Right"
                                    >
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-1"></th>
                                @endif
                            @endif

                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                <x-filament-tables::selection.cell tag="th">
                                    <x-filament-tables::selection.checkbox
                                        :label="__('filament-tables::table.fields.bulk_select_page.label')"
                                        x-bind:checked="
                                            const recordsOnPage = getRecordsOnPage()

                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                $el.checked = true

                                                return 'checked'
                                            }

                                            $el.checked = false

                                            return null
                                        "
                                        x-on:click="toggleSelectRecordsOnPage"
                                    />
                                </x-filament-tables::selection.cell>
                            @endif

                            @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells)
                                @if ($actionsColumnLabel)
                                    <x-filament-tables::header-cell
                                        :alignment="Alignment::Right"
                                    >
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-1"></th>
                                @endif
                            @endif
                        @endif
                    </x-slot>

                    @if ($isColumnSearchVisible)
                        <x-filament-tables::row>
                            @if ($isReordering)
                                <td></td>
                            @else
                                @if (count($actions) && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
                                    <td></td>
                                @endif

                                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                    <td></td>
                                @endif
                            @endif

                            @foreach ($columns as $column)
                                <x-filament-tables::cell
                                    @class([
                                        'fi-table-individual-search-cell-' . str($column->getName())->camel()->kebab(),
                                        'px-3 py-2',
                                    ])
                                >
                                    @if ($column->isIndividuallySearchable())
                                        <x-filament-tables::search-field
                                            wire-model="tableColumnSearches.{{ $column->getName() }}"
                                        />
                                    @endif
                                </x-filament-tables::cell>
                            @endforeach

                            @if (! $isReordering)
                                @if (count($actions) && in_array($actionsPosition, [ActionsPosition::AfterColumns, ActionsPosition::AfterCells]))
                                    <td></td>
                                @endif

                                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                    <td></td>
                                @endif
                            @endif
                        </x-filament-tables::row>
                    @endif
                    <x-filament-tables::row>
                    @if (($records !== null) && count($records))
                        @php
                            $isRecordRowStriped = false;
                            $previousRecord = null;
                            $previousRecordGroupKey = null;
                            $previousRecordGroupTitle = null;
                        @endphp
                        @foreach($headerLabels as $column)
							@php
								$value = 0;
								if(in_array($this->data['group'],['points_added','issues_added'])){
									$max = explode("-",$column)[0];
									if((int)$max==0){
										$max = 99999999999999999999;
										$min = 101;
									}
									$value = $records->where('total','<=',$max)->where('total','>=',$min)->count();
									$record = null;
								}else{
									$record = $records->where('grouped_by',$column)->first();
								}

							@endphp

							@if($record)
								@php $value = $record->total @endphp
							@endif
                            @php
                                $groupHeaderColspan = $columnsCount;

                                if ($isSelectionEnabled) {
                                    $groupHeaderColspan--;

                                    if (
                                        ($recordCheckboxPosition === RecordCheckboxPosition::BeforeCells) &&
                                        count($actions) &&
                                        ($actionsPosition === ActionsPosition::BeforeCells)
                                    ) {
                                        $groupHeaderColspan--;
                                    }
                                }
                            @endphp

                            <td class="p-0">
                            	{{ $value }}
                            </td>


							@php
								$total +=$value;
							@endphp
						@endforeach
						</x-filament-tables::row>


                        @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle) && ((! $records instanceof \Illuminate\Contracts\Pagination\Paginator) || (! $records->hasMorePages())))
                            <x-filament-tables::summary.row
                                :actions="count($actions)"
                                :actions-position="$actionsPosition"
                                :columns="$columns"
                                :groups-only="$isGroupsOnly"
                                :heading="$isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                                :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                :record-checkbox-position="$recordCheckboxPosition"
                                :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                                :selection-enabled="$isSelectionEnabled"
                            />
                        @endif

                        @if ($hasSummary && (! $isReordering))
                            <x-filament-tables::summary
                                :actions="count($actions)"
                                :actions-position="$actionsPosition"
                                :columns="$columns"
                                :groups-only="$isGroupsOnly"
                                :plural-model-label="$pluralModelLabel"
                                :record-checkbox-position="$recordCheckboxPosition"
                                :records="$records"
                                :selection-enabled="$isSelectionEnabled"
                            />
                        @endif

                        @if ($contentFooter)
                            <x-slot name="footer">
                                {{
                                    $contentFooter->with([
                                        'columns' => $columns,
                                        'records' => $records,
                                    ])
                                }}
                            </x-slot>
                        @endif
                    @endif
                </x-filament-tables::table>
            @elseif ($records === null)
                <div class="h-32"></div>
            @elseif ($emptyState = $getEmptyState())
                {{ $emptyState }}
            @else
                <tr>
                    <td colspan="{{ $columnsCount }}">
                        <x-filament-tables::empty-state
                            :actions="$getEmptyStateActions()"
                            :description="$getEmptyStateDescription()"
                            :heading="$getEmptyStateHeading()"
                            :icon="$getEmptyStateIcon()"
                        />
                    </td>
                </tr>
            @endif
        </div>

        @if ($records instanceof \Illuminate\Contracts\Pagination\Paginator && ((! ($records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)) || $records->total()))
            <x-filament::pagination
                :page-options="$getPaginationPageOptions()"
                :paginator="$records"
                class="px-3 py-3 sm:px-6"
            />
        @endif

        @if ($hasFiltersBelowContent)
            <x-filament-tables::filters
                :form="$getFiltersForm()"
                class="p-4 sm:px-6"
            />
        @endif
    </x-filament-tables::container>

    <x-filament-actions::modals />
</div>
