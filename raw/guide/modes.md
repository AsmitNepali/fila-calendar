# Modes

> Single, multiple, range, and multi-range selection modes.

Use `Asmit\FilaCalendar\Support\CalendarMode` for type-safe mode selection.

## Single

```php
use Asmit\FilaCalendar\Support\CalendarMode;

CalendarInput::make('date')
    ->mode(CalendarMode::Single);
```

State: `"2026-07-10"`

## Multiple

```php
CalendarInput::make('dates')
    ->mode(CalendarMode::Multiple);
```

State: `["2026-07-02", "2026-07-10", "2026-07-15"]`

## Range

```php
CalendarInput::make('range')
    ->mode(CalendarMode::Range);
```

Click a start date, then an end date. Blocked dates inside the span are skipped automatically.

State: `{ "start": "2026-07-02", "end": "2026-07-10" }`

## Multi-range

```php
CalendarInput::make('ranges')
    ->mode(CalendarMode::MultiRange);
```

Click start, then end to add a range. Repeat to add more ranges. Click an existing range's start/end to remove it, or a middle day to split it.

State:

```php
[
    ['start' => '2026-07-02', 'end' => '2026-07-04'],
    ['start' => '2026-07-10', 'end' => '2026-07-12'],
]
```

## Hover preview

In `range` and `multi-range` modes, after choosing a start date, hovering toward the end date previews the in-between days with a soft highlight before the second click.
