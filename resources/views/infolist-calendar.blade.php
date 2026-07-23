@php
    $hydratedState = $getState();
    $initialDate = match (true) {
        is_string($hydratedState) => $hydratedState,
        is_array($hydratedState) && isset($hydratedState['start']) => $hydratedState['start'],
        is_array($hydratedState) && isset($hydratedState[0]['start']) => $hydratedState[0]['start'],
        is_array($hydratedState) && isset($hydratedState[0]) && is_string($hydratedState[0]) => $hydratedState[0],
        default => now()->toDateString(),
    };
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @include('fila-calendar::components.calendar-widget', [
        'readOnly' => true,
        'disabled' => false,
        'hydratedState' => $hydratedState,
        'statePath' => null,
        'initialDate' => $initialDate,
    ])
</x-dynamic-component>
