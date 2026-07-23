# Filament Calendar

A Flux-style inline calendar field for [Filament](https://filamentphp.com) — no Flux dependency.

## Documentation

- Local docs: `cd docs && npm install && npm run dev`
- GitHub Pages: https://asmitnepali.github.io/filament-calendar/

## Install

```bash
composer require asmitnepali/filament-calendar
php artisan filament:assets
```

## Quick example

```php
use Asmitnepali\FilamentCalendar\Forms\Components\CalendarInput;

CalendarInput::make('availabilities')
    ->mode('multi-range')
    ->months(12)
    ->calendarColumns(4)
    ->withToday()
    ->locale('ja');
```

## License

MIT
