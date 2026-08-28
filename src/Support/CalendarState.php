<?php

namespace Asmit\FilaCalendar\Support;

use Carbon\Carbon;

/**
 * @internal
 */
class CalendarState
{
    /**
     * Normalize external state for the Alpine calendar UI.
     *
     * @return array{start: string, end: string}|list<array{start: string, end: string}>|list<string>|string|null
     */
    public static function hydrate(mixed $state, ?CalendarMode $mode, bool $multiple = false): mixed
    {
        if ($state === null || $state === '' || $state === []) {
            return self::emptyState($mode, $multiple);
        }

        if ($mode === CalendarMode::Range) {
            return self::hydrateRange($state);
        }

        if ($mode === CalendarMode::MultiRange) {
            return self::hydrateMultiRange($state);
        }

        if ($mode === CalendarMode::Single || ($mode === null && ! $multiple)) {
            return self::hydrateSingle($state);
        }

        if ($mode === CalendarMode::Multiple || $multiple) {
            if (! is_array($state) || DateRanges::isRangeList($state)) {
                return [];
            }

            return collect($state)
                ->filter(fn ($date): bool => filled($date))
                ->map(fn ($date): string => Carbon::parse((string) $date)->toDateString())
                ->unique()
                ->sort()
                ->values()
                ->all();
        }

        return null;
    }

    /**
     * Normalize Alpine state for form dehydration / persistence.
     *
     * @return array{start: string, end: string}|list<array{start: string, end: string}>|list<string>|string|null
     */
    public static function dehydrate(mixed $state, ?CalendarMode $mode, bool $multiple = false): mixed
    {
        if ($state === null || $state === '' || $state === []) {
            return self::emptyState($mode, $multiple);
        }

        if ($mode === CalendarMode::Range) {
            return self::dehydrateRange($state);
        }

        if ($mode === CalendarMode::MultiRange) {
            return self::dehydrateMultiRange($state);
        }

        if ($mode === CalendarMode::Single || ($mode === null && ! $multiple)) {
            return self::dehydrateSingle($state);
        }

        if ($mode === CalendarMode::Multiple || $multiple) {
            if (! is_array($state) || DateRanges::isRangeList($state)) {
                return [];
            }

            return collect($state)
                ->filter(fn ($date): bool => filled($date))
                ->map(fn ($date): string => $date instanceof Carbon ? $date->toDateString() : (string) $date)
                ->unique()
                ->sort()
                ->values()
                ->all();
        }

        return null;
    }

    protected static function hydrateSingle(mixed $state): ?string
    {
        if (is_array($state)) {
            $date = $state['start'] ?? $state[0] ?? null;

            if (blank($date)) {
                return null;
            }

            return Carbon::parse((string) $date)->toDateString();
        }

        return Carbon::parse((string) $state)->toDateString();
    }

    protected static function dehydrateSingle(mixed $state): ?string
    {
        if (is_array($state)) {
            $date = $state['start'] ?? $state[0] ?? null;

            if (blank($date)) {
                return null;
            }

            return $date instanceof Carbon ? $date->toDateString() : (string) $date;
        }

        return $state instanceof Carbon ? $state->toDateString() : (string) $state;
    }

    /**
     * @return array{start: string, end: string}|null
     */
    protected static function hydrateRange(mixed $state): ?array
    {
        if (! is_array($state)) {
            return null;
        }

        $start = $state['start'] ?? $state[0] ?? null;
        $end = $state['end'] ?? $state[1] ?? null;

        if (blank($start) || blank($end)) {
            return null;
        }

        return [
            'start' => Carbon::parse((string) $start)->toDateString(),
            'end' => Carbon::parse((string) $end)->toDateString(),
        ];
    }

    /**
     * @return list<array{start: string, end: string}>
     */
    protected static function hydrateMultiRange(mixed $state): array
    {
        if (! is_array($state) || ! DateRanges::isRangeList($state)) {
            return [];
        }

        return collect($state)
            ->map(function (mixed $range): ?array {
                if (! is_array($range)) {
                    return null;
                }

                $start = $range['start'] ?? null;
                $end = $range['end'] ?? null;

                if (blank($start) || blank($end)) {
                    return null;
                }

                return [
                    'start' => Carbon::parse((string) $start)->toDateString(),
                    'end' => Carbon::parse((string) $end)->toDateString(),
                ];
            })
            ->filter()
            ->sortBy('start')
            ->values()
            ->all();
    }

    /**
     * @return array{start: string, end: string}|null
     */
    protected static function dehydrateRange(mixed $state): ?array
    {
        if (! is_array($state)) {
            return null;
        }

        $start = $state['start'] ?? $state['from'] ?? null;
        $end = $state['end'] ?? $state['until'] ?? null;

        if (blank($start) || blank($end)) {
            return null;
        }

        return [
            'start' => $start instanceof Carbon ? $start->toDateString() : (string) $start,
            'end' => $end instanceof Carbon ? $end->toDateString() : (string) $end,
        ];
    }

    /**
     * @return list<array{start: string, end: string}>
     */
    protected static function dehydrateMultiRange(mixed $state): array
    {
        if (! is_array($state) || ! DateRanges::isRangeList($state)) {
            return [];
        }

        return collect($state)
            ->map(function (mixed $range): ?array {
                if (! is_array($range)) {
                    return null;
                }

                $start = $range['start'] ?? null;
                $end = $range['end'] ?? null;

                if (blank($start) || blank($end)) {
                    return null;
                }

                return [
                    'start' => $start instanceof Carbon ? $start->toDateString() : (string) $start,
                    'end' => $end instanceof Carbon ? $end->toDateString() : (string) $end,
                ];
            })
            ->filter()
            ->sortBy('start')
            ->values()
            ->all();
    }

    /**
     * @return array{start: string, end: string}|list<array{start: string, end: string}>|list<string>|string|null
     */
    protected static function emptyState(?CalendarMode $mode, bool $multiple): mixed
    {
        return match ($mode) {
            CalendarMode::Range => null,
            CalendarMode::MultiRange, CalendarMode::Multiple => [],
            default => $multiple ? [] : null,
        };
    }
}
