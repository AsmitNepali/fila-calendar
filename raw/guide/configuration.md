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
      Grid columns on wide screens
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

## Unavailable vs weekend

- `unavailableDates()` marks specific dates with a strikethrough style.
- `weekEndDays()` blocks recurring weekdays with a muted style.

Both are skipped when building ranges and hover previews.
