<input {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}" />

@php debug($getStatePath()) @endphp
