<?php

namespace Asmit\FilaCalendar\Support;

/**
 * @internal
 */
class Locale
{
    public static function normalize(?string $locale): ?string
    {
        if (blank($locale)) {
            return null;
        }

        $locale = strtolower(str_replace('_', '-', trim($locale)));

        return match ($locale) {
            'jp' => 'ja',
            default => $locale,
        };
    }

    public static function resolve(?string $locale): string
    {
        return self::normalize($locale)
            ?? self::normalize(app()->getLocale())
            ?? 'en';
    }
}
