@php
    use Filament\Support\Facades\FilamentAsset;

    $calendarLocale = $getResolvedLocale();

    $calendarMonthOptions = collect(range(0, 11))->map(fn (int $month): array => [
        'value' => $month,
        'label' => \Illuminate\Support\Carbon::create(2000, $month + 1, 1)
            ->locale($calendarLocale)
            ->translatedFormat('F'),
    ]);

    $calendarYearRange = range(((int) now()->format('Y')) - 50, ((int) now()->format('Y')) + 50);

    $calendarAlpineSrc = FilamentAsset::getAlpineComponentSrc('fila-calendar', package: 'asmit/fila-calendar');
    $calendarAlpinePath = public_path('js/asmit/fila-calendar/components/fila-calendar.js');
    $calendarAlpineSrc .= '&t='.(is_file($calendarAlpinePath) ? filemtime($calendarAlpinePath) : time());
@endphp

<div
    @class([
        'fi-fila-calendar',
        'fi-fila-calendar--scrollable' => $isScrollable(),
        'fi-fila-calendar--readonly' => $readOnly,
        "fi-fila-calendar--{$getSize()}" => filled($getSize()),
    ])
    style="--fi-calendar-columns: {{ $getCalendarColumns() }}"
    @unless ($readOnly)
        wire:ignore
    @endunless
    x-load
    x-load-src="{{ $calendarAlpineSrc }}"
    x-data="filaCalendar({
        state: @js($hydratedState),
        statePath: @js($statePath ?? null),
        readOnly: @js((bool) $readOnly),
        mode: @js($getResolvedMode()->value),
        minDate: @js($getMinDate()),
        maxDate: @js($getMaxDate()),
        unavailableDates: @js($getUnavailableDates()),
        disabledDates: @js($getDisabledDates()),
        weekEndDays: @js($getWeekEndDays()),
        months: @js($getMonths()),
        selectableHeader: @js($getSelectableHeader()),
        withToday: @js($getWithToday()),
        showAdjacentMonths: @js($getShowAdjacentMonths()),
        disabled: @js($disabled),
        initialDate: @js($initialDate),
        locale: @js($getLocale()),
    })"
>
    <div class="fi-fila-calendar__toolbar">
        <button
            type="button"
            class="fi-fila-calendar__nav"
            x-on:click="previousMonth()"
            x-bind:disabled="disabled"
            aria-label="{{ __('fila-calendar::calendar.previous_month') }}"
        >
            <x-filament::icon icon="heroicon-m-chevron-left" class="fi-fila-calendar__nav-icon" />
        </button>

        <div class="fi-fila-calendar__toolbar-center">
            <template x-if="selectableHeader">
                <div class="fi-fila-calendar__selects">
                    <x-filament::input.wrapper
                        alpine-disabled="disabled"
                        class="fi-fila-calendar__select-field"
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
                        class="fi-fila-calendar__select-field fi-fila-calendar__select-field--year"
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
                <span class="fi-fila-calendar__toolbar-label" x-text="toolbarLabel()"></span>
            </template>
        </div>

        <div class="fi-fila-calendar__toolbar-actions">
            <button
                type="button"
                class="fi-fila-calendar__nav"
                x-on:click="nextMonth()"
                x-bind:disabled="disabled"
                aria-label="{{ __('fila-calendar::calendar.next_month') }}"
            >
                <x-filament::icon icon="heroicon-m-chevron-right" class="fi-fila-calendar__nav-icon" />
            </button>

            <x-filament::button
                type="button"
                size="sm"
                color="gray"
                x-show="withToday"
                x-on:click="goToToday()"
                x-bind:disabled="disabled"
            >
                {{ __('fila-calendar::calendar.today') }}
            </x-filament::button>
        </div>
    </div>

    <div class="fi-fila-calendar__viewport">
        <div class="fi-fila-calendar__months">
            <template x-for="(monthDate, monthIndex) in monthsToRender()" :key="monthIndex">
                <section class="fi-fila-calendar__month">
                    <header class="fi-fila-calendar__header">
                        <span class="fi-fila-calendar__label" x-text="monthLabel(monthDate)"></span>
                    </header>

                    <div class="fi-fila-calendar__weekdays">
                        <template x-for="weekday in weekdayLabels" :key="weekday">
                            <span class="fi-fila-calendar__weekday" x-text="weekday"></span>
                        </template>
                    </div>

                    <div
                        class="fi-fila-calendar__days"
                        x-on:mouseleave="clearHoveredDate()"
                    >
                        <template x-for="day in calendarDays(monthDate)" :key="day.key">
                            <button
                                type="button"
                                x-bind:class="day.placeholder ? 'fi-calendar-day fi-calendar-day--placeholder' : dayClasses(day, hoverRevision, state)"
                                x-bind:disabled="day.placeholder || isButtonDisabled(day.date)"
                                x-bind:aria-hidden="day.placeholder ? 'true' : undefined"
                                x-bind:tabindex="day.placeholder ? '-1' : undefined"
                                x-on:click="if (! day.placeholder) selectDate(day.date)"
                                x-on:mouseenter="if (! day.placeholder) setHoveredDate(day.date)"
                            >
                                <span class="fi-calendar-day__number" x-text="day.placeholder ? '' : day.day"></span>
                            </button>
                        </template>
                    </div>
                </section>
            </template>
        </div>
    </div>
</div>
