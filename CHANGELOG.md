# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- CSS variables for range start, end, and middle day colors so host apps can theme the calendar without overriding component classes.

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

[Unreleased]: https://github.com/AsmitNepali/fila-calendar/compare/v0.0.2...HEAD
[0.0.2]: https://github.com/AsmitNepali/fila-calendar/compare/v0.0.1...v0.0.2
[0.0.1]: https://github.com/AsmitNepali/fila-calendar/compare/v0.0.0...v0.0.1
[0.0.0]: https://github.com/AsmitNepali/fila-calendar/releases/tag/v0.0.0
