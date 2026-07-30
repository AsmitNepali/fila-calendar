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
@endphp

<div
    @class([
        'fi-fila-calendar',
        'fi-fila-calendar--scrollable' => $isScrollable(),
        'fi-fila-calendar--readonly' => $readOnly,
        "fi-fila-calendar--{$getSize()}" => filled($getSize()),
    ])
    style="{{ $getCalendarColumnsStyle() }}"
    @unless ($readOnly)
        wire:ignore
    @endunless
    x-load
    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('fila-calendar', package: 'asmit/fila-calendar') }}"
    x-data="filaCalendar({
        @if ($readOnly)
            state: @js($hydratedState),
            readOnly: true,
        @else
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            readOnly: false,
        @endif
        mode: @js($getResolvedMode()->value),
        minDate: @js($getMinDate()),
        maxDate: @js($getMaxDate()),
        unavailableDates: @js($getUnavailableDates()),
        disabledDates: @js($getDisabledDates()),
        reservedDates: @js($getReservedDates()),
        weekEndDays: @js($getWeekEndDays()),
        months: @js($getMonths()),
        selectableHeader: @js($getSelectableHeader()),
        withToday: @js($getWithToday()),
        showAdjacentMonths: @js($getShowAdjacentMonths()),
        disabled: @js($disabled),
        initialDate: @js($initialDate),
        locale: @js($getLocale()),
        weekStartsOn: @js($getWeekStartsOn()),
        i18n: @js([
            'selected' => __('fila-calendar::calendar.announcements.selected'),
            'deselected' => __('fila-calendar::calendar.announcements.deselected'),
            'range_started' => __('fila-calendar::calendar.announcements.range_started'),
            'range_selected' => __('fila-calendar::calendar.announcements.range_selected'),
            'cleared' => __('fila-calendar::calendar.announcements.cleared'),
        ]),
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

    <div
        class="fi-fila-calendar__announcer"
        role="status"
        aria-live="polite"
        aria-atomic="true"
        x-text="announcement"
    ></div>

    <div class="fi-fila-calendar__viewport">
        <div class="fi-fila-calendar__months">
            <template x-for="(monthDate, monthIndex) in monthsToRender()" :key="monthIndex">
                <section class="fi-fila-calendar__month">
                    <header class="fi-fila-calendar__header">
                        <span class="fi-fila-calendar__label" x-text="monthLabel(monthDate)"></span>
                    </header>

                    <div
                        class="fi-fila-calendar__grid"
                        role="grid"
                        x-bind:aria-label="monthLabel(monthDate)"
                    >
                        <div class="fi-fila-calendar__weekdays" role="row">
                            <template x-for="(weekday, weekdayIndex) in weekdayLabels" :key="weekday">
                                <span
                                    class="fi-fila-calendar__weekday"
                                    role="columnheader"
                                    x-bind:aria-label="weekdayLongLabels[weekdayIndex]"
                                    x-text="weekday"
                                ></span>
                            </template>
                        </div>

                        <div
                            class="fi-fila-calendar__days"
                            role="rowgroup"
                            x-on:mouseleave="clearHoveredDate()"
                        >
                            <template x-for="(week, weekIndex) in calendarWeeks(monthDate)" :key="weekIndex">
                                <div class="fi-fila-calendar__week" role="row">
                                    <template x-for="day in week" :key="day.key">
                                        <button
                                            type="button"
                                            role="gridcell"
                                            x-bind:class="day.placeholder ? 'fi-calendar-day fi-calendar-day--placeholder' : dayClasses(day, hoverRevision)"
                                            x-bind:disabled="day.placeholder || isInteractionBlocked(day.date)"
                                            x-bind:aria-hidden="day.placeholder ? 'true' : undefined"
                                            x-bind:aria-selected="day.placeholder ? undefined : isSelected(day.date)"
                                            x-bind:aria-current="! day.placeholder && isToday(day.date) ? 'date' : false"
                                            x-bind:aria-label="day.placeholder ? undefined : dayLabel(day)"
                                            x-bind:tabindex="day.placeholder ? '-1' : dayTabIndex(day)"
                                            x-bind:title="! day.placeholder && isReservedDate(day.date) ? @js($getReservedTooltip()) : null"
                                            x-bind:data-calendar-date="day.placeholder ? false : day.date"
                                            x-bind:data-current-month="day.currentMonth"
                                            x-on:click="! day.placeholder && selectDate(day.date, $event)"
                                            x-on:keydown="! day.placeholder && onDayKeydown($event, day.date)"
                                            x-on:focus="day.placeholder || (focusedDate = day.date)"
                                            x-on:mouseenter="! day.placeholder && setHoveredDate(day.date)"
                                        >
                                            <span class="fi-calendar-day__number" x-text="day.placeholder ? '' : day.day"></span>

                                            <template x-if="! day.placeholder && isReservedDate(day.date)">
                                                <x-filament::icon
                                                    :icon="$getReservedIcon()"
                                                    class="fi-calendar-day__icon"
                                                    aria-label="{{ $getReservedTooltip() ?? __('fila-calendar::calendar.reserved') }}"
                                                />
                                            </template>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </section>
            </template>
        </div>
    </div>
</div>
