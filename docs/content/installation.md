---
title: Installation
description: Install the package in a Laravel Filament application.
navigation:
  order: 2
---

# Installation

Install the package with Composer:

```bash
composer require asmitnepali/filament-calendar
```

If you use a path repository during development:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/filament-calendar/"
        }
    ],
    "require": {
        "asmitnepali/filament-calendar": "@dev"
    }
}
```

## Publish assets

Register the Alpine component and stylesheet with Filament:

```bash
php artisan filament:assets
```

## Theme CSS

Import the package stylesheet into your Filament theme so Vite compiles the calendar styles:

```css
@import "../../../../../packages/filament-calendar/resources/css/filament-calendar.css";
```

Adjust the relative path for your project layout.

## Auto-discovery

The service provider is registered automatically. No manual provider setup is required.
