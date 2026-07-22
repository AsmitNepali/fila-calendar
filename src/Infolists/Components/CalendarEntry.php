<?php

namespace Asmitnepali\FilamentCalendar\Infolists\Components;

use Asmitnepali\FilamentCalendar\Concerns\HasCalendarConfiguration;
use Asmitnepali\FilamentCalendar\Support\CalendarState;
use Filament\Infolists\Components\Entry;

class CalendarEntry extends Entry
{
    use HasCalendarConfiguration;

    protected string $view = 'filament-calendar::infolist-calendar';

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(
            fn (mixed $state): mixed => CalendarState::hydrate($state, $this->getResolvedMode(), $this->isMultiple()),
        );

        $this->columnSpanFull();
    }
}
