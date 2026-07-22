<?php

namespace Asmitnepali\FilamentCalendar\Forms\Components;

use Asmitnepali\FilamentCalendar\Concerns\HasCalendarConfiguration;
use Asmitnepali\FilamentCalendar\Support\CalendarState;
use Filament\Forms\Components\Field;

class CalendarInput extends Field
{
    use HasCalendarConfiguration;

    protected string $view = 'filament-calendar::calendar';

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
