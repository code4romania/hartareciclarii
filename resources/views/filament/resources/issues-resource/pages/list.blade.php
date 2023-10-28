<x-filament-panels::page
@class([
	'fi-resource-view-record-page',
	'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),

	])
	>
	<x-filament::tabs>
        @foreach ($this->getTabs() as $tabKey => $tab)
            @php
                $tabKey = strval($tabKey);
            @endphp

            <a href="{{ $tab->getUrl() }}">
            	<x-filament::tabs.item
	                :active="$tab->isActive()"
	                :badge="$tab->getBadge()"
	                :badge-color="$tab->getBadgeColor()"
	                :icon="$tab->getIcon()"
	            >
	                {{ $tab->getLabel() ?? $this->generateTabLabel($tabKey) }}
	            </x-filament::tabs.item>
        	</a>
        @endforeach

    </x-filament::tabs>

    {{ $this->form }}
        <x-filament-actions::actions
		        :actions="$this->getCachedFormActions()"

		        :full-width="$this->hasFullWidthFormActions()"
		    />
        {{-- <x-filament-forms wire:submit.prevent="generate">
            {{ $this->form }}

            <x-filament-actions::actions
		        :actions="$this->getCachedFormActions()"
		        :alignment="$getAlignment()"
		        :full-width="$isFullWidth()"
		    />
        </x-filament-forms> --}}
    {{ $this->table }}

</x-filament-panels>