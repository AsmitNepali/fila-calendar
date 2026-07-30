<?php

namespace Asmit\FilaCalendar\Forms\Components;

use Asmit\FilaCalendar\Concerns\HasCalendarConfiguration;
use Asmit\FilaCalendar\Support\CalendarMode;
use Asmit\FilaCalendar\Support\CalendarState;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Field;

class CalendarInput extends Field
{
    use HasCalendarConfiguration;

    protected string $view = 'fila-calendar::calendar';

    protected int|Closure|null $minNights = null;

    protected int|Closure|null $maxNights = null;

    protected int|Closure|null $minDates = null;

    protected int|Closure|null $maxDates = null;

    protected bool|Closure $requireCompleteRange = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(
            fn (mixed $state): mixed => CalendarState::hydrate($state, $this->getResolvedMode(), $this->isMultiple()),
        );

        $this->dehydrateStateUsing(
            fn (mixed $state): mixed => CalendarState::dehydrate($state, $this->getResolvedMode(), $this->isMultiple()),
        );

        $this->rule(
            fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                foreach ($this->getSelectionErrors($value) as $message) {
                    $fail($message);
                }
            },
        );
    }

    public function minNights(int|Closure|null $nights): static
    {
        $this->minNights = $nights;

        return $this;
    }

    public function getMinNights(): ?int
    {
        $nights = $this->evaluate($this->minNights);

        return is_numeric($nights) ? (int) $nights : null;
    }

    public function maxNights(int|Closure|null $nights): static
    {
        $this->maxNights = $nights;

        return $this;
    }

    public function getMaxNights(): ?int
    {
        $nights = $this->evaluate($this->maxNights);

        return is_numeric($nights) ? (int) $nights : null;
    }

    public function minDates(int|Closure|null $dates): static
    {
        $this->minDates = $dates;

        return $this;
    }

    public function getMinDates(): ?int
    {
        $dates = $this->evaluate($this->minDates);

        return is_numeric($dates) ? (int) $dates : null;
    }

    public function maxDates(int|Closure|null $dates): static
    {
        $this->maxDates = $dates;

        return $this;
    }

    public function getMaxDates(): ?int
    {
        $dates = $this->evaluate($this->maxDates);

        return is_numeric($dates) ? (int) $dates : null;
    }

    public function requireCompleteRange(bool|Closure $condition = true): static
    {
        $this->requireCompleteRange = $condition;

        return $this;
    }

    public function isRequiringCompleteRange(): bool
    {
        return (bool) $this->evaluate($this->requireCompleteRange);
    }

    /**
     * Validate a selection against the configured range constraints.
     *
     * @return list<string>
     */
    public function getSelectionErrors(mixed $state): array
    {
        $mode = $this->getResolvedMode();
        $selection = $this->parseSelection($state, $mode);

        if ($selection === null) {
            return [__('fila-calendar::calendar.validation.invalid')];
        }

        $errors = [];
        $nights = [];
        $selectedDates = 0;
        $hasIncompleteRange = false;

        foreach ($selection as $range) {
            if ($range['end'] === null) {
                $hasIncompleteRange = true;
                $selectedDates += 1;

                continue;
            }

            $rangeNights = (int) Carbon::parse($range['start'])->diffInDays($range['end']);

            $nights[] = $rangeNights;
            $selectedDates += $rangeNights + 1;
        }

        if ($hasIncompleteRange && $this->isRequiringCompleteRange()) {
            $errors[] = __('fila-calendar::calendar.validation.incomplete_range');
        }

        $minNights = $this->getMinNights();
        $maxNights = $this->getMaxNights();

        if ($minNights !== null && $nights !== [] && min($nights) < $minNights) {
            $errors[] = trans_choice('fila-calendar::calendar.validation.min_nights', $minNights, ['min' => $minNights]);
        }

        if ($maxNights !== null && $nights !== [] && max($nights) > $maxNights) {
            $errors[] = trans_choice('fila-calendar::calendar.validation.max_nights', $maxNights, ['max' => $maxNights]);
        }

        $minDates = $this->getMinDates();
        $maxDates = $this->getMaxDates();

        if ($minDates !== null && ($selectedDates > 0 || $this->isRequired()) && $selectedDates < $minDates) {
            $errors[] = trans_choice('fila-calendar::calendar.validation.min_dates', $minDates, ['min' => $minDates]);
        }

        if ($maxDates !== null && $selectedDates > $maxDates) {
            $errors[] = trans_choice('fila-calendar::calendar.validation.max_dates', $maxDates, ['max' => $maxDates]);
        }

        return $errors;
    }

    /**
     * Flatten any mode's state into date ranges. Single dates become one night-less range.
     * Returns `null` when the payload contains something that is not a date.
     *
     * @return list<array{start: string, end: string|null}>|null
     */
    protected function parseSelection(mixed $state, CalendarMode $mode): ?array
    {
        if (blank($state)) {
            return [];
        }

        if ($mode === CalendarMode::Range) {
            return $this->parseRange(is_array($state) ? $state : ['start' => $state]);
        }

        if ($mode === CalendarMode::MultiRange) {
            if (! is_array($state)) {
                return null;
            }

            $ranges = [];

            foreach ($state as $range) {
                if (! is_array($range)) {
                    return null;
                }

                $parsed = $this->parseRange($range);

                if ($parsed === null) {
                    return null;
                }

                $ranges = [...$ranges, ...$parsed];
            }

            return $ranges;
        }

        if ($mode === CalendarMode::Multiple) {
            if (! is_array($state)) {
                return null;
            }

            $ranges = [];

            foreach ($state as $date) {
                if (blank($date)) {
                    continue;
                }

                $normalized = $this->normalizeDate($date);

                if ($normalized === null) {
                    return null;
                }

                $ranges[] = ['start' => $normalized, 'end' => $normalized];
            }

            return $ranges;
        }

        $normalized = $this->normalizeDate(is_array($state) ? ($state['start'] ?? null) : $state);

        return $normalized === null ? null : [['start' => $normalized, 'end' => $normalized]];
    }

    /**
     * @param  array<mixed>  $range
     * @return list<array{start: string, end: string|null}>|null
     */
    protected function parseRange(array $range): ?array
    {
        $start = $this->normalizeDate($range['start'] ?? $range[0] ?? null);
        $end = blank($range['end'] ?? $range[1] ?? null)
            ? null
            : $this->normalizeDate($range['end'] ?? $range[1] ?? null);

        if ($start === null) {
            return blank($range['start'] ?? $range[0] ?? null) ? [] : null;
        }

        if ($end === null && filled($range['end'] ?? $range[1] ?? null)) {
            return null;
        }

        if ($end !== null && $end < $start) {
            [$start, $end] = [$end, $start];
        }

        return [['start' => $start, 'end' => $end]];
    }

    protected function normalizeDate(mixed $date): ?string
    {
        if ($date instanceof Carbon) {
            return $date->toDateString();
        }

        if (! is_string($date) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) !== 1) {
            return null;
        }

        [$year, $month, $day] = array_map(intval(...), explode('-', $date));

        return checkdate($month, $day, $year) ? $date : null;
    }
}
