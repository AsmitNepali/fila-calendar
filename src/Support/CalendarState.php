<?php

namespace Asmitnepali\FilamentCalendar\Support;

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
    public static function hydrate(mixed $state, ?string $mode, bool $multiple = false): mixed
    {
        if ($state === null || $state === '' || $state === []) {
            return self::emptyState($mode, $multiple);
        }

        if ($mode === 'range') {
            return self::hydrateRange($state);
        }

        if ($mode === 'multi-range') {
            return self::hydrateMultiRange($state);
        }

        if ($mode === 'single' || ($mode === null && ! $multiple)) {
            if (is_array($state)) {
                return null;
            }

            return Carbon::parse((string) $state)->toDateString();
        }

        if ($mode === 'multiple' || $multiple) {
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
    public static function dehydrate(mixed $state, ?string $mode, bool $multiple = false): mixed
    {
        if ($state === null || $state === '' || $state === []) {
            return self::emptyState($mode, $multiple);
        }

        if ($mode === 'range') {
            return self::dehydrateRange($state);
        }

        if ($mode === 'multi-range') {
            return self::dehydrateMultiRange($state);
        }

        if ($mode === 'single' || ($mode === null && ! $multiple)) {
            if (is_array($state)) {
                return null;
            }

            return Carbon::parse((string) $state)->toDateString();
        }

        if ($mode === 'multiple' || $multiple) {
            if (! is_array($state)) {
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
    protected static function emptyState(?string $mode, bool $multiple): mixed
    {
        return match ($mode) {
            'range' => null,
            'multi-range', 'multiple' => [],
            default => $multiple ? [] : null,
        };
    }
}
