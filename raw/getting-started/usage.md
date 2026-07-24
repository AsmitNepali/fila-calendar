# Usage

> Form fields, infolist entries, and state formats.

## Form field

```php
use Asmit\FilaCalendar\Forms\Components\CalendarInput;
use Asmit\FilaCalendar\Support\CalendarMode;

CalendarInput::make('dates')
    ->label('Dates')
    ->mode(CalendarMode::Range)
    ->required();
```

## Read-only infolist entry

```php
use Asmit\FilaCalendar\Infolists\Components\CalendarEntry;
use Asmit\FilaCalendar\Support\CalendarMode;

CalendarEntry::make('dates')
    ->label('Saved dates')
    ->mode(CalendarMode::MultiRange)
    ->state(fn (): array => [
        ['start' => '2026-07-01', 'end' => '2026-07-10'],
    ]);
```

## State formats

<table>
<thead>
  <tr>
    <th>
      Mode
    </th>
    
    <th>
      Stored state
    </th>
  </tr>
</thead>

<tbody>
  <tr>
    <td>
      <code>
        single
      </code>
    </td>
    
    <td>
      <code>
        "2026-07-10"
      </code>
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        multiple
      </code>
    </td>
    
    <td>
      <code>
        ["2026-07-02", "2026-07-10"]
      </code>
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        range
      </code>
    </td>
    
    <td>
      <code>
        { "start": "2026-07-02", "end": "2026-07-10" }
      </code>
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        multi-range
      </code>
    </td>
    
    <td>
      <code>
        [{ "start": "2026-07-02", "end": "2026-07-04" }, ...]
      </code>
    </td>
  </tr>
</tbody>
</table>

`CalendarInput` hydrates and dehydrates these formats automatically through `CalendarState`.

## Interaction model

- Selection logic runs in Alpine.js on the client.
- Form fields bind with Livewire `$entangle`.
- No server round-trip is required for each click.
- Save/submit reads the final entangled state.

## Multi-range deselect behavior

<table>
<thead>
  <tr>
    <th>
      Click
    </th>
    
    <th>
      Result
    </th>
  </tr>
</thead>

<tbody>
  <tr>
    <td>
      Start or end day
    </td>
    
    <td>
      Removes the whole range
    </td>
  </tr>
  
  <tr>
    <td>
      Middle day
    </td>
    
    <td>
      Splits into two ranges around the removed day
    </td>
  </tr>
  
  <tr>
    <td>
      Single-day range
    </td>
    
    <td>
      Removes that day
    </td>
  </tr>
</tbody>
</table>

Example: range `1–10`, click day `5` → `1–4` and `6–10`.
