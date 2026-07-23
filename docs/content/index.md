---
title: Introduction
description: A Flux-style inline calendar field for Filament — no Flux dependency.
navigation:
  order: 1
---

# Filament Calendar

`asmitnepali/filament-calendar` is a standalone Filament calendar field inspired by Flux Calendar. It works with Filament forms and infolists, supports multiple selection modes, and ships its own Alpine.js UI and styles.

## Features

- Single, range, multiple, and multi-range selection
- Form field (`CalendarInput`) and read-only infolist entry (`CalendarEntry`)
- Multi-month grid with responsive columns
- Unavailable dates, weekend blocking, and min/max bounds
- Range hover preview while selecting
- Middle-day deselect splits a range instead of clearing it
- Locale support via `->locale('ja')`
- No Flux dependency

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Filament 4 or 5

## Quick start

```php
use Asmitnepali\FilamentCalendar\Forms\Components\CalendarInput;

CalendarInput::make('available_dates')
    ->mode('multi-range')
    ->months(3)
    ->calendarColumns(3)
    ->withToday()
    ->minDate(now()->toDateString());
```

Browse the sidebar for installation, usage, modes, configuration, and localization.
