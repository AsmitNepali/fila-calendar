@php
    use Filament\Support\Facades\FilamentAsset;

    $calendarMonthOptions = collect(range(0, 11))->map(fn (int $month): array => [
        'value' => $month,
        'label' => \Illuminate\Support\Carbon::create(2000, $month + 1, 1)->format('F'),
    ]);

    $calendarYearRange = range(((int) now()->format('Y')) - 50, ((int) now()->format('Y')) + 50);
@endphp

<div
    @class([
        'fi-filament-calendar',
        'fi-filament-calendar--scrollable' => $isScrollable(),
        'fi-filament-calendar--readonly' => $readOnly,
        "fi-filament-calendar--{$getSize()}" => filled($getSize()),
    ])
    style="--fi-calendar-columns: {{ $getCalendarColumns() }}"
    @unless ($readOnly)
        wire:ignore
    @endunless
    x-load
    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('filament-calendar', package: 'asmitnepali/filament-calendar') }}"
    x-data="filamentCalendar({
        @if ($readOnly)
            state: @js($hydratedState),
            readOnly: true,
        @else
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            readOnly: false,
        @endif
        mode: @js($getResolvedMode()),
        minDate: @js($getMinDate()),
        maxDate: @js($getMaxDate()),
        unavailableDates: @js($getUnavailableDates()),
        disabledDates: @js($getDisabledDates()),
        weekEndDays: @js($getWeekEndDays()),
        months: @js($getMonths()),
        selectableHeader: @js($getSelectableHeader()),
        withToday: @js($getWithToday()),
        disabled: @js($disabled),
        initialDate: @js($initialDate),
    })"
>
    <div class="fi-filament-calendar__toolbar">
        <button
            type="button"
            class="fi-filament-calendar__nav"
            x-on:click="previousMonth()"
            x-bind:disabled="disabled"
            aria-label="{{ __('filament-calendar::calendar.previous_month') }}"
        >
            <x-filament::icon icon="heroicon-m-chevron-left" class="fi-filament-calendar__nav-icon" />
        </button>

        <div class="fi-filament-calendar__toolbar-center">
            <template x-if="selectableHeader">
                <div class="fi-filament-calendar__selects">
                    <x-filament::input.wrapper
                        alpine-disabled="disabled"
                        class="fi-filament-calendar__select-field"
                    >
                        <x-filament::input.select
                            x-bind:value="viewStart.getMonth()"
                            x-on:change="setMonth(0, $event.target.value)"
                            x-bind:disabled="disabled"
                        >
                            @foreach ($calendarMonthOptions as $monthOption)
                                <option value="{{ $monthOption['value'] }}">
                                    {{ $monthOption['label'] }}
                                </option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>

                    <x-filament::input.wrapper
                        alpine-disabled="disabled"
                        class="fi-filament-calendar__select-field fi-filament-calendar__select-field--year"
                    >
                        <x-filament::input.select
                            x-bind:value="viewStart.getFullYear()"
                            x-on:change="setYear(0, $event.target.value)"
                            x-bind:disabled="disabled"
                        >
                            @foreach ($calendarYearRange as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </template>

            <template x-if="! selectableHeader">
                <span class="fi-filament-calendar__toolbar-label" x-text="toolbarLabel()"></span>
            </template>
        </div>

        <div class="fi-filament-calendar__toolbar-actions">
            <button
                type="button"
                class="fi-filament-calendar__nav"
                x-on:click="nextMonth()"
                x-bind:disabled="disabled"
                aria-label="{{ __('filament-calendar::calendar.next_month') }}"
            >
                <x-filament::icon icon="heroicon-m-chevron-right" class="fi-filament-calendar__nav-icon" />
            </button>

            <x-filament::button
                type="button"
                size="sm"
                color="gray"
                x-show="withToday"
                x-on:click="goToToday()"
                x-bind:disabled="disabled"
            >
                {{ __('filament-calendar::calendar.today') }}
            </x-filament::button>
        </div>
    </div>

    <div class="fi-filament-calendar__viewport">
        <div class="fi-filament-calendar__months">
            <template x-for="(monthDate, monthIndex) in monthsToRender()" :key="monthIndex">
                <section class="fi-filament-calendar__month">
                    <header class="fi-filament-calendar__header">
                        <span class="fi-filament-calendar__label" x-text="monthLabel(monthDate)"></span>
                    </header>

                    <div class="fi-filament-calendar__weekdays">
                        <template x-for="weekday in weekdayLabels" :key="weekday">
                            <span class="fi-filament-calendar__weekday" x-text="weekday"></span>
                        </template>
                    </div>

                    <div class="fi-filament-calendar__days">
                        <template x-for="day in calendarDays(monthDate)" :key="day.date">
                            <button
                                type="button"
                                x-bind:class="dayClasses(day)"
                                x-bind:disabled="isButtonDisabled(day.date)"
                                x-bind:aria-disabled="isInteractionBlocked(day.date)"
                                x-on:click="selectDate(day.date)"
                            >
                                <span class="fi-calendar-day__number" x-text="day.day"></span>
                            </button>
                        </template>
                    </div>
                </section>
            </template>
        </div>
    </div>
</div>
