<?php

namespace Asmitnepali\FilamentCalendar\Support;

use Carbon\Carbon;
use Carbon\Month;
use Carbon\WeekDay;
use DateTimeInterface;

/**
 * @internal
 */
class DateRanges
{
    /**
     * @param  array<int, string|Carbon|DateTimeInterface>|string  $dates
     * @return list<array{start: Carbon, end: Carbon}>
     */
    public static function compute(array|string $dates): array
    {
        $ranges = [];

        collect(self::normalizeInput($dates))
            ->filter(fn ($date): bool => filled($date))
            ->map(fn (DateTimeInterface|WeekDay|Month|string|int|float|null $date) => Carbon::parse($date)->startOfDay())
            ->sort()
            ->unique(fn (Carbon $date): string => $date->toDateString())
            ->values()
            ->each(function (Carbon $date) use (&$ranges): void {
                if ($ranges === []) {
                    $ranges[] = [
                        'start' => $date,
                        'end' => $date,
                    ];

                    return;
                }

                $lastIndex = array_key_last($ranges);

                if ($date->isSameDay($ranges[$lastIndex]['end']->copy()->addDay())) {
                    $ranges[$lastIndex]['end'] = $date;

                    return;
                }

                $ranges[] = [
                    'start' => $date,
                    'end' => $date,
                ];
            });

        return $ranges;
    }

    /**
     * @param  array<int, array{start: mixed, end: mixed}>  $ranges
     * @return list<string>
     */
    public static function expand(array $ranges): array
    {
        $dates = [];

        foreach ($ranges as $range) {
            $current = Carbon::parse($range['start'])->startOfDay();
            $end = Carbon::parse($range['end'])->startOfDay();

            while ($current->lte($end)) {
                $dates[] = $current->toDateString();
                $current = $current->copy()->addDay();
            }
        }

        return $dates;
    }

    /**
     * @param  list<array{start: Carbon, end: Carbon}>  $ranges
     * @return list<array{start: string, end: string}>
     */
    public static function toRangeStrings(array $ranges): array
    {
        return array_values(
            collect($ranges)
                ->map(fn (array $range): array => [
                    'start' => $range['start']->toDateString(),
                    'end' => $range['end']->toDateString(),
                ])
                ->all(),
        );
    }

    /**
     * @return list<string>
     */
    public static function normalizeInput(mixed $state): array
    {
        if (is_string($state)) {
            return array_values(array_filter(array_map(
                trim(...),
                explode(',', $state),
            )));
        }

        if (! is_array($state)) {
            return [];
        }

        if (self::isRangeList($state)) {
            return self::expand($state);
        }

        return array_values($state);
    }

    /**
     * @param  list<string>  $blockedDates
     * @return list<array{start: string, end: string}>
     */
    public static function splitAroundBlockedDates(string $start, string $end, array $blockedDates): array
    {
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $blocked = array_fill_keys($blockedDates, true);
        $ranges = [];
        $currentStart = null;
        $currentEnd = null;
        $current = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->startOfDay();

        while ($current->lte($endDate)) {
            $dateString = $current->toDateString();

            if (isset($blocked[$dateString])) {
                if ($currentStart !== null) {
                    $ranges[] = ['start' => $currentStart, 'end' => $currentEnd];
                    $currentStart = null;
                    $currentEnd = null;
                }
            } elseif ($currentStart === null) {
                $currentStart = $dateString;
                $currentEnd = $dateString;
            } else {
                $currentEnd = $dateString;
            }

            if ($current->equalTo($endDate)) {
                break;
            }

            $current = $current->copy()->addDay();
        }

        if ($currentStart !== null) {
            $ranges[] = ['start' => $currentStart, 'end' => $currentEnd];
        }

        return $ranges;
    }

    /**
     * @param  array<int, mixed>  $state
     */
    public static function isRangeList(array $state): bool
    {
        if ($state === []) {
            return false;
        }

        $first = reset($state);

        return is_array($first) && array_key_exists('start', $first) && array_key_exists('end', $first);
    }
}
