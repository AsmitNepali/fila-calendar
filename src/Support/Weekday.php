<?php

namespace Asmitnepali\FilamentCalendar\Support;

class Weekday
{
    public static function normalize(mixed $day): ?int
    {
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
}
