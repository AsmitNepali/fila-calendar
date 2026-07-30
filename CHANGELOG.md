# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.0] - 2026-07-30

### Changed

- A range drawn across reserved days now splits around them instead of covering them. Dragging from the 11th to the 19th over bookings on the 12th, 13th and 17th yields three ranges, so an already-booked day is never selected again, and the hover preview shows the same gaps while dragging. In `range` mode, where a single range cannot hold a gap, the selection stops at the booking and keeps the run of free days touching the day it started from. `unavailableDates()` and `weekEndDays()` are still spanned.
- `reservedDates()` is UI-only again. Server-side reserved-day validation was removed from `CalendarInput`; host apps that need to reject reserved days on save should enforce that themselves.

## [0.3.1] - 2026-07-30

### Fixed

- Past days inside a range rendered at full range color instead of the faded version added in 0.2.0. Two later rules in the stylesheet re-declared `--disabled.--in-range` and `--disabled.--range-end` without the fade, so only a range's start day read as past. Each of start, end and middle now fades its own color variable.

## [0.3.0] - 2026-07-30

### Added

- `reservedDates()` marks days that are already taken rather than closed. They block selection like unavailable dates, but keep the day's own background and carry a corner icon, a hairline ring in the primary color, and a `Reserved` tooltip, so "booked by someone else" no longer looks identical to "closed by a rule".
- `reservedIcon()` sets the marker icon. Accepts a Filament `ScalableIcon` — every `Heroicon` enum case is one — or a plain icon name. Defaults to `Heroicon::Bookmark`.
- `reservedTooltip()` overrides the tooltip text on reserved days, which also feeds the icon's `aria-label`. Pass an empty string to drop the tooltip and keep the icon.
- `calendarColumns()` accepts a column count per breakpoint, e.g. `['sm' => 2, 'lg' => 3, 'xl' => 4]`. Keys are `default`, `sm`, `md`, `lg`, `xl` and `2xl` (`xxl` and `xs` are accepted as aliases), matching Tailwind's widths. Each value holds until a wider breakpoint overrides it, and counts are still clamped to `months()`.
- `--fi-fila-calendar-reserved-color` CSS variable for theming the reserved ring and marker.
- Server-side validation on `CalendarInput` rejecting a submission that lands on a reserved day, including one covered by a range. A crafted Livewire payload can no longer double-book. `unavailableDates()` and `weekEndDays()` stay UI-only, so records already sitting on those dates keep saving.

### Fixed

- `disabledDates()` and `unavailableDates()` silently produced no blocked days when given `Carbon` instances or datetime strings, such as a date column plucked off a model. Values are now normalized to `Y-m-d` on both the PHP and JavaScript sides, and unparseable entries are dropped instead of reaching the browser as `NaN`.

### Changed

- The `title` tooltip removed in 0.2.0 returns for reserved days only.

## [0.2.0] - 2026-07-29

### Changed

- Ranges now span blocked days instead of splitting around them. A blocked day still cannot start or end a range, but one drawn across unavailable, weekend, or out-of-bounds days stays a single range.
- Blocked days inside a range render with the range highlight instead of the blocked style, so a long span no longer looks broken up.
- Blocked days are now rendered as disabled buttons, so they no longer take hover, focus, or click styling.
- Shift-clicking a range keeps the blocked days inside it selected instead of clearing them along with the rest.
- Today is marked with a dot instead of a recolored number, so it stays readable on top of a range color.
- Past days inside a range keep a faded version of the range color.

### Removed

- Title tooltip on calendar days.

## [0.1.0] - 2026-07-29

### Added

- CSS variables for range start, end, and middle day colors so host apps can theme the calendar without overriding component classes.
- Shift-click a day of a range in `multi-range` mode to clear the whole range.

### Fixed

- Deselecting a day in `multi-range` mode took two clicks when ranges overlapped. Drawing a range across an existing one appended an overlapping range instead of merging it, so the first click only split one of the two ranges covering the day. Ranges are now merged whenever state is read and written.

### Changed

- Clicking the start or end day of a range in `multi-range` mode now shrinks the range by that day instead of clearing the range. Shift-click clears the whole range.

## [0.0.2] - 2026-07-28

### Fixed

- Carbon constraint now requires `^3.0`. The package uses the `Carbon\Month` and `Carbon\WeekDay` enums, which do not exist in Carbon 2, so a Carbon 2 resolution installed a broken package.

### Changed

- Removed the explicit `illuminate/support` requirement. The supported Laravel range is now dictated by Filament, so the package no longer has to be re-tagged for each Laravel release.

## [0.0.1] - 2026-07-24

### Fixed

- Single-mode calendar state hydration when form state is a string or range-shaped array.
- Docs site 404 flash on hard refresh.
- Docs navigation icons and active state on client-side navigation and page refresh.

### Changed

- Split development setup from production installation in the documentation.
- Docs deployment workflow now pushes built files directly to the `gh-pages` branch.

## [0.0.0] - 2026-07-23

### Added

- Inline calendar form field for Filament with single, multiple, range, and multi-range selection modes.
- Read-only `CalendarEntry` infolist component.
- Flat range hover preview and middle-day deselect range splitting.
- Locale configuration for calendar labels.
- Multi-month grid with responsive columns, date constraints, and weekend blocking.
- Nuxt documentation site with GitHub Pages deployment.

[Unreleased]: https://github.com/AsmitNepali/fila-calendar/compare/v0.4.0...HEAD
[0.4.0]: https://github.com/AsmitNepali/fila-calendar/compare/v0.3.1...v0.4.0
[0.3.1]: https://github.com/AsmitNepali/fila-calendar/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/AsmitNepali/fila-calendar/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/AsmitNepali/fila-calendar/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/AsmitNepali/fila-calendar/compare/v0.0.2...v0.1.0
[0.0.2]: https://github.com/AsmitNepali/fila-calendar/compare/v0.0.1...v0.0.2
[0.0.1]: https://github.com/AsmitNepali/fila-calendar/compare/v0.0.0...v0.0.1
[0.0.0]: https://github.com/AsmitNepali/fila-calendar/releases/tag/v0.0.0
