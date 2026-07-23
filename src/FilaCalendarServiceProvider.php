<?php

namespace Asmit\FilaCalendar;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilaCalendarServiceProvider extends PackageServiceProvider
{
    public static string $name = 'fila-calendar';

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
            AlpineComponent::make('fila-calendar', __DIR__.'/../resources/js/fila-calendar.js'),
            Css::make('fila-calendar', __DIR__.'/../resources/css/fila-calendar.css'),
        ], 'asmit/fila-calendar');
    }
}
