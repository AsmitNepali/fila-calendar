<?php

namespace Asmit\FilaCalendar\Support;

use Carbon\Carbon;

enum Weekday: int
{
    case Sunday = 0;

    case Monday = 1;

    case Tuesday = 2;

    case Wednesday = 3;

    case Thursday = 4;

    case Friday = 5;

    case Saturday = 6;

    public static function normalize(mixed $day): ?int
    {
        if ($day instanceof self) {
            return $day->value;
        }

        if (is_int($day) && $day >= 0 && $day <= 6) {
            return $day;
        }

        if (! is_string($day)) {
            return null;
        }

        return match (strtoupper(trim($day))) {
            'SUN', 'SUNDAY' => 0,
            'MON', 'MONDAY' => 1,
            'TUE', 'TUESDAY' => 2,
            'WED', 'WEDNESDAY' => 3,
            'THU', 'THURSDAY' => 4,
            'FRI', 'FRIDAY' => 5,
            'SAT', 'SATURDAY' => 6,
            default => null,
        };
    }

    /**
     * @param  list<mixed>  $days
     * @return list<int>
     */
    public static function normalizeMany(array $days): array
    {
        return collect($days)
            ->map(fn (mixed $day): ?int => self::normalize($day))
            ->filter(fn (?int $day): bool => $day !== null)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * The first day of the week for a locale, as an `Intl`-style index (0 = Sunday).
     */
    public static function firstDayForLocale(?string $locale): int
    {
        $locale = Locale::normalize($locale);

        // ponytail: bare `en` keeps the historical Sunday default; use `en-gb`
        // or ->weekStartsOn() for anything else.
        if ($locale === null || $locale === 'en') {
            return self::Sunday->value;
        }

        return Carbon::create(2000, 1, 1)
            ->locale($locale)
            ->startOfWeek()
            ->dayOfWeek;
    }
}
