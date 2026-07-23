---
title: Configuration
description: Calendar layout, bounds, and disabled dates.
navigation:
  order: 5
---

# Configuration

All options are available on both `CalendarInput` and `CalendarEntry`.

```php
CalendarInput::make('dates')
    ->mode('multi-range')
    ->months(12)
    ->calendarColumns(4)
    ->size('xs')
    ->scrollable(false)
    ->selectableHeader()
    ->withToday()
    ->minDate(now()->toDateString())
    ->maxDate(now()->addYear()->toDateString())
    ->unavailableDates(['2026-07-15', '2026-07-20'])
    ->weekEndDays(['sat', 'sun'])
    ->locale('ja');
```

## Options

| Method | Description |
| --- | --- |
| `mode()` | `single`, `multiple`, `range`, or `multi-range` |
| `months()` | Number of months to render (1–12) |
| `calendarColumns()` | Grid columns on wide screens |
| `size()` | Size variant, e.g. `xs` |
| `scrollable()` | Scroll the month grid inside a viewport |
| `selectableHeader()` | Month/year dropdowns in the toolbar |
| `withToday()` | Show a Today button |
| `minDate()` / `maxDate()` | Bounds as `Y-m-d` strings |
| `unavailableDates()` | Dates that cannot be selected |
| `weekEndDays()` | Weekdays treated as blocked (`0`–`6`, names, or `sun`/`mon`/…) |
| `locale()` | Locale for month and weekday labels |

## Unavailable vs weekend

- `unavailableDates()` marks specific dates with a strikethrough style.
- `weekEndDays()` blocks recurring weekdays with a muted style.

Both are skipped when building ranges and hover previews.
