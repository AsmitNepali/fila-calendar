@php
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @include('filament-calendar::components.calendar-widget', [
        'readOnly' => false,
        'disabled' => $isDisabled(),
        'hydratedState' => null,
        'statePath' => $statePath,
        'initialDate' => now()->toDateString(),
    ])
</x-dynamic-component>
