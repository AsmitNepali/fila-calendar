<?php

namespace Asmit\FilaCalendar\Infolists\Components;

use Asmit\FilaCalendar\Concerns\HasCalendarConfiguration;
use Asmit\FilaCalendar\Support\CalendarState;
use Filament\Infolists\Components\Entry;

class CalendarEntry extends Entry
{
    use HasCalendarConfiguration;

    protected string $view = 'fila-calendar::infolist-calendar';

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(
            fn (mixed $state): mixed => CalendarState::hydrate($state, $this->getResolvedMode(), $this->isMultiple()),
        );

        $this->columnSpanFull();
    }
}
