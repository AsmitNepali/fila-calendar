# asmit/fila-calendar

<img src="art/cover.png" alt="Fila Calendar" width="800">

A polished inline calendar field for Filament forms and infolists. Supports single, range, multiple, and multi-range selection with a self-contained Alpine.js UI — no external calendar library required.

[![Latest Version](https://img.shields.io/packagist/v/asmit/fila-calendar.svg?style=for-the-badge)](https://packagist.org/packages/asmit/fila-calendar)
[![PHP 8.2+](https://img.shields.io/badge/php-8.2%2B-blue.svg?style=for-the-badge)](https://php.net)
[![Laravel 11+](https://img.shields.io/badge/laravel-11%2B-red.svg?style=for-the-badge)](https://laravel.com)
[![Filament 4+](https://img.shields.io/badge/filament-4%2B-f59e0b.svg?style=for-the-badge)](https://filamentphp.com)

## Features

- **Multiple selection modes** — single, multiple, range, and multi-range
- **Form + infolist** — `CalendarInput` field and read-only `CalendarEntry`
- **Multi-month grid** — responsive columns with configurable month count
- **Date constraints** — unavailable dates, weekend blocking, and min/max bounds
- **Range hover preview** — visual feedback while selecting a range
- **Smart range editing** — deselecting a middle day splits the range instead of clearing it
- **Localization** — calendar labels via `->locale('ja')`
- **No extra dependencies** — ships its own Alpine.js component and styles

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Filament 4 or 5

## Installation

```bash
composer require asmit/fila-calendar
php artisan filament:assets
```

Import the package stylesheet into your Filament theme:

```css
@import '../../vendor/asmit/fila-calendar/resources/css/fila-calendar.css';
```

## Usage

Add a calendar field to a Filament form:

```php
use Asmit\FilaCalendar\Forms\Components\CalendarInput;
use Asmit\FilaCalendar\Support\CalendarMode;

CalendarInput::make('booking')
    ->mode(CalendarMode::MultiRange)
    ->months(12)
    ->calendarColumns(4)
    ->withToday()
    ->locale('ja');
```

Render saved dates as a read-only infolist entry:

```php
use Asmit\FilaCalendar\Infolists\Components\CalendarEntry;
use Asmit\FilaCalendar\Support\CalendarMode;

CalendarEntry::make('booking')
    ->mode(CalendarMode::MultiRange)
    ->months(3)
    ->calendarColumns(3);
```

**[View Complete Documentation →](https://asmitnepali.github.io/fila-calendar/)**

## Contributing

Contributions are welcome. Please see [CONTRIBUTING.md](CONTRIBUTING.md) before opening an issue or pull request.

## Security

If you discover a security issue, please review our [Security Policy](SECURITY.md).

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for recent changes.

## License

MIT License. See [LICENSE.md](LICENSE.md) for details.
