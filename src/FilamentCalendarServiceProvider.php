<?php

namespace Asmitnepali\FilamentCalendar;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentCalendarServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-calendar';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            AlpineComponent::make('filament-calendar', __DIR__.'/../resources/js/filament-calendar.js'),
            Css::make('filament-calendar', __DIR__.'/../resources/css/filament-calendar.css'),
        ], 'asmitnepali/filament-calendar');
    }
}
