---
title: Modes
description: Single, multiple, range, and multi-range selection modes.
navigation:
  order: 4
---

# Modes

## Single

```php
CalendarInput::make('date')
    ->mode('single');
```

State: `"2026-07-10"`

## Multiple

```php
CalendarInput::make('dates')
    ->mode('multiple');
```

State: `["2026-07-02", "2026-07-10", "2026-07-15"]`

## Range

```php
CalendarInput::make('range')
    ->mode('range');
```

Click a start date, then an end date. Blocked dates inside the span are skipped automatically.

State: `{ "start": "2026-07-02", "end": "2026-07-10" }`

## Multi-range

```php
CalendarInput::make('ranges')
    ->mode('multi-range');
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
