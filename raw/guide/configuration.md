# Configuration

> Calendar layout, bounds, and disabled dates.

All options are available on both `CalendarInput` and `CalendarEntry`.

```php
use Asmit\FilaCalendar\Forms\Components\CalendarInput;
use Asmit\FilaCalendar\Support\CalendarMode;

CalendarInput::make('dates')
    ->mode(CalendarMode::MultiRange)
    ->months(12)
    ->calendarColumns(4)
    ->size('xs')
    ->scrollable(false)
    ->selectableHeader()
    ->withToday()
    ->minDate(now()->toDateString())
    ->maxDate(now()->addYear()->toDateString())
    ->unavailableDates(['2026-07-15', '2026-07-20'])
    ->reservedDates(['2026-07-26', '2026-07-27'])
    ->weekEndDays(['sat', 'sun'])
    ->locale('ja');
```

## Options

<table>
<thead>
  <tr>
    <th>
      Method
    </th>
    
    <th>
      Description
    </th>
  </tr>
</thead>

<tbody>
  <tr>
    <td>
      <code>
        mode()
      </code>
    </td>
    
    <td>
      <code>
        CalendarMode::Single
      </code>
      
      , <code>
        Multiple
      </code>
      
      , <code>
        Range
      </code>
      
      , or <code>
        MultiRange
      </code>
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        months()
      </code>
    </td>
    
    <td>
      Number of months to render (1–12)
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        calendarColumns()
      </code>
    </td>
    
    <td>
      Grid columns — a number, or one per breakpoint
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        size()
      </code>
    </td>
    
    <td>
      Size variant, e.g. <code>
        xs
      </code>
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        scrollable()
      </code>
    </td>
    
    <td>
      Scroll the month grid inside a viewport
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        selectableHeader()
      </code>
    </td>
    
    <td>
      Month/year dropdowns in the toolbar
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        withToday()
      </code>
    </td>
    
    <td>
      Show a Today button
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        showAdjacentMonths()
      </code>
      
       / <code>
        hideAdjacentMonths()
      </code>
    </td>
    
    <td>
      Render the previous/next month days that pad the first and last week (default: shown)
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        minDate()
      </code>
      
       / <code>
        maxDate()
      </code>
    </td>
    
    <td>
      Bounds as <code>
        Y-m-d
      </code>
      
       strings
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        unavailableDates()
      </code>
    </td>
    
    <td>
      Dates that cannot be selected
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        reservedDates()
      </code>
    </td>
    
    <td>
      Dates already taken — blocked, marked with an icon
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        reservedIcon()
      </code>
    </td>
    
    <td>
      Icon for reserved days — <code>
        ScalableIcon
      </code>
      
       or icon name (default: <code>
        Heroicon::Bookmark
      </code>
      
      )
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        reservedTooltip()
      </code>
    </td>
    
    <td>
      Tooltip on reserved days (default: translated <code>
        Reserved
      </code>
      
      )
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        weekEndDays()
      </code>
    </td>
    
    <td>
      Weekdays treated as blocked (<code>
        0
      </code>
      
      –<code>
        6
      </code>
      
      , names, or <code>
        sun
      </code>
      
      /<code>
        mon
      </code>
      
      /…)
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        locale()
      </code>
    </td>
    
    <td>
      Locale for month and weekday labels
    </td>
  </tr>
</tbody>
</table>

## Responsive columns

`calendarColumns()` takes a single number, or one per breakpoint:

```php
CalendarInput::make('dates')
    ->months(12)
    ->calendarColumns([
        'sm' => 2,
        'lg' => 3,
        'xl' => 4,
    ]);
```

Keys are `default`, `sm`, `md`, `lg`, `xl` and `2xl`, matching Tailwind's widths (640px, 768px,
1024px, 1280px, 1536px). `xxl` is accepted as an alias for `2xl`, and `xs` for `default`. Each value holds until a wider breakpoint overrides it, so the example
above renders 2 columns from 640px, 3 from 1024px and 4 from 1280px. Below 640px the months always
stack in a single column, so a phone gets one month at a time whatever you configure.

A column count is never allowed to exceed `months()`, and closures are supported:

```php
->calendarColumns(fn (): array => $this->isCompact() ? ['sm' => 1] : ['sm' => 2, 'lg' => 4])
```

Omit the option to get the default: 2 columns for up to 2 months, 3 for up to 6, otherwise 4.

## Adjacent month days

By default the first and last week of a month are padded with days from the previous and next month. Pass `hideAdjacentMonths()` (or `showAdjacentMonths(false)`) to drop them:

```php
CalendarInput::make('dates')
    ->hideAdjacentMonths();
```

The padding cells stay in the grid so every month keeps its seven columns, but they render empty: not clickable, not focusable, and never hovered or selected.

## Unavailable vs reserved vs weekend

All three block selection. They differ in what they tell the person looking at the calendar:

- `unavailableDates()` — closed by a rule. Strikethrough style.
- `reservedDates()` — open, but already taken by an existing booking. Keeps the day's normal background; marked with a hairline ring, a corner icon, and a `Reserved` tooltip. Days already filled by a range selection skip the ring, since the range color anchors the icon.
- `weekEndDays()` — recurring blocked weekdays. Muted style.

None of them can be clicked, so none can start or end a range. They differ in what happens when a
range is drawn *across* them:

- `unavailableDates()` and `weekEndDays()` are spanned. The range stays continuous and those days
are highlighted as part of it, because they describe rules about the calendar rather than days
someone else already holds.
- `reservedDates()` break the range. Dragging from the 11th to the 19th across bookings on the
12th, 13th and 17th produces three ranges — 11th, 14th–16th and 18th–19th — so a booked day is
never selected twice. The hover preview shows the same gaps while dragging.

In `range` mode a single range cannot hold a gap, so the selection stops at the booking it runs
into, keeping the run of free days that still touches the day the selection started from.

The marker uses the panel's primary color on plain days and stays white on days filled by a range
selection. Override the color per theme with `--fi-fila-calendar-reserved-color`:

```css
.fi-fila-calendar {
    --fi-fila-calendar-reserved-color: var(--danger-500);
}
```

### Choosing the icon

The icon defaults to `Heroicon::Bookmark`. Pass a `ScalableIcon` — every case of Filament's
`Heroicon` enum is one — and Filament picks the variant that fits the rendered size:

```php
use Asmit\FilaCalendar\Forms\Components\CalendarInput;
use Filament\Support\Icons\Heroicon;

CalendarInput::make('availabilities')
    ->reservedDates(['2026-08-11', '2026-08-12'])
    ->reservedIcon(Heroicon::EllipsisHorizontal);
```

Plain icon names still work, for icon sets that have no enum:

```php
->reservedIcon('heroicon-m-lock-closed')
```

Closures work for both, so the icon can follow the record:

```php
->reservedIcon(fn (): Heroicon => $this->getRecord()->isProvisional()
    ? Heroicon::Clock
    : Heroicon::LockClosed)
```

### Tooltip

Reserved days carry a `Reserved` tooltip, also used as the icon's `aria-label`. Override the text
with `reservedTooltip()`:

```php
CalendarInput::make('availabilities')
    ->reservedDates($dates)
    ->reservedTooltip('Booked by another job');
```

It accepts a closure as well. Pass an empty string to drop the tooltip and keep the icon; the
`aria-label` falls back to the translated `Reserved` label so the marker stays announced:

```php
->reservedTooltip('')
```

`reservedDates()` accepts `Y-m-d` strings, `Carbon` instances, or anything else `Carbon::parse()`
understands, so a column plucked straight off a model works:

```php
->reservedDates(fn (): array => Booking::query()
    ->whereBetween('date', [$availableFrom, $availableTo])
    ->pluck('date')
    ->all())
```

Use bounds for the window itself (`minDate()` / `maxDate()`) and `reservedDates()` for the taken
days inside it.

`reservedDates()` is UI-only (same as `unavailableDates()` and `weekEndDays()`). Host apps that
need to reject reserved days on save should add their own form rules or split ranges before
hydrating state.

## Range colors

Selections, ranges, the today ring, and the reserved marker follow the panel's primary color out of the box, so a calendar matches an amber or rose panel with no CSS at all. The built-in amber palette — Filament's own default primary — is only the fallback for a calendar rendered outside a Filament panel.

Override any of it from the host app theme by setting CSS variables on `.fi-fila-calendar` (or a parent):

```css
.fi-fila-calendar {
    --fi-fila-calendar-range-start-bg: var(--primary-500);
    --fi-fila-calendar-range-start-color: white;
    --fi-fila-calendar-range-end-bg: var(--primary-600);
    --fi-fila-calendar-range-end-color: white;
    --fi-fila-calendar-range-middle-bg: color-mix(in oklab, var(--primary-500) 18%, transparent);
    --fi-fila-calendar-range-middle-color: var(--primary-600);
    --fi-fila-calendar-range-middle-hover-bg: color-mix(in oklab, var(--primary-500) 28%, transparent);
    --fi-fila-calendar-range-pending-ring: currentColor;
}
```

Use Filament's runtime palette variables (`--primary-500`, `--primary-600`, …) injected on `:root`. Do **not** use `--color-primary-500` here — that name is a Tailwind `@theme inline` alias for utility classes and is not available to arbitrary CSS.

Single-day selections and range starts use the start variables. Range ends use the end variables. Days between the start and end use the middle variables. `--fi-fila-calendar-range-pending-ring` is the ring on a range's start day while it waits for the second click; it defaults to that day's text color so it stays legible on the range fill.

A light primary color can leave the default white number hard to read on a filled day. Set `--fi-fila-calendar-range-start-color` and `--fi-fila-calendar-range-end-color` to a dark value in that case.

Rebuild the Filament theme after changing host CSS (`npm run dev` or `npm run build`).
