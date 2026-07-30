<?php

namespace Asmit\FilaCalendar\Forms\Components;

use Asmit\FilaCalendar\Concerns\HasCalendarConfiguration;
use Asmit\FilaCalendar\Support\CalendarState;
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
    }
}
