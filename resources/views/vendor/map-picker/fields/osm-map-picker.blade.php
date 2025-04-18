<x-filament-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hintActions="$getHintActions()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
        <div x-data="mapPicker($wire, {{ $getMapConfig() }})"
            x-init="async () => {
            do {
                await (new Promise(resolve => setTimeout(resolve, 100)));
            } while (!$refs.map);
            attach($refs.map);
        }" wire:ignore>
        <div
            x-ref="map"
            class="w-full" style="min-height: 30vh; z-index: 1 !important; {{ $getExtraStyle() }}">
        </div>
        <input type="text" id="{{ $getId() }}_fmrest" style="display:none"/>
    </div>
</x-filament-forms::field-wrapper>
