<?php

namespace Asmit\FilaCalendar\Forms\Components;

use Asmit\FilaCalendar\Concerns\HasCalendarConfiguration;
use Asmit\FilaCalendar\Support\CalendarState;
use Asmit\FilaCalendar\Support\DateRanges;
use Closure;
use Filament\Forms\Components\Field;

class CalendarInput extends Field
{
    use HasCalendarConfiguration;

    protected string $view = 'fila-calendar::calendar';

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(
            fn (mixed $state): mixed => CalendarState::hydrate($state, $this->getResolvedMode(), $this->isMultiple()),
        );

        $this->dehydrateStateUsing(
            fn (mixed $state): mixed => CalendarState::dehydrate($state, $this->getResolvedMode(), $this->isMultiple()),
        );

        // Blocking reserved days in the UI only stops honest clicks; a crafted Livewire
        // payload would still double-book, so reject them server-side too.
        $this->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
            $reserved = $this->getReservedDates();

            if ($reserved === []) {
                return;
            }

            $clashes = array_values(array_unique(array_intersect(DateRanges::flatten($value), $reserved)));

            if ($clashes === []) {
                return;
            }

            $fail(__('fila-calendar::calendar.reserved_validation', [
                'dates' => implode(', ', $clashes),
            ]));
        });
    }
}
