<?php

namespace Asmit\FilaCalendar\Concerns;

use Asmit\FilaCalendar\Support\CalendarMode;
use Asmit\FilaCalendar\Support\Locale;
use Asmit\FilaCalendar\Support\Weekday;
use Carbon\Carbon;
use Closure;
use DateTimeInterface;

trait HasCalendarConfiguration
{
    protected int $months = 1;

    protected ?CalendarMode $mode = null;

    protected ?string $minDate = null;

    protected ?string $maxDate = null;

    protected bool|Closure|null $scrollable = true;

    protected int|Closure|null $calendarColumns = null;

    protected string|Closure|null $size = null;

    protected bool|Closure|null $withToday = null;

    protected bool|Closure $showAdjacentMonths = true;

    protected bool|Closure|null $selectableHeader = null;

    protected bool|Closure $multiple = false;

    /** @var list<string>|Closure */
    protected array|Closure $disabledDates = [];

    /** @var list<string>|Closure */
    protected array|Closure $reservedDates = [];

    /** @var list<string|int>|Closure */
    protected array|Closure $weekEndDays = [];

    protected string|Closure|null $locale = null;

    public function locale(string|Closure|null $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): ?string
    {
        $locale = $this->evaluate($this->locale);

        return Locale::normalize(is_string($locale) ? $locale : null);
    }

    public function getResolvedLocale(): string
    {
        return Locale::resolve($this->getLocale());
    }

    public function minDate(string|Closure $date): static
    {
        $this->minDate = $date;

        return $this;
    }

    public function getMinDate(): ?string
    {
        $date = $this->evaluate($this->minDate);

        return is_string($date) ? $date : null;
    }

    public function maxDate(string|Closure $date): static
    {
        $this->maxDate = $date;

        return $this;
    }

    public function getMaxDate(): ?string
    {
        $date = $this->evaluate($this->maxDate);

        return is_string($date) ? $date : null;
    }

    public function months(int $months): static
    {
        $this->months = max(1, min($months, 12));

        return $this;
    }

    public function mode(CalendarMode $mode): static
    {
        $this->mode = $mode;

        return $this;
    }

    public function calendarColumns(int|Closure|null $columns): static
    {
        $this->calendarColumns = $columns;

        return $this;
    }

    public function multiple(bool|Closure $condition = true): static
    {
        $this->multiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        if ($this->getMode() === CalendarMode::Multiple) {
            return true;
        }

        return (bool) ($this->evaluate($this->multiple) ?? false);
    }

    public function scrollable(bool|Closure $condition = true): static
    {
        $this->scrollable = $condition;

        return $this;
    }

    public function size(string|Closure|null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function withToday(bool|Closure $condition = true): static
    {
        $this->withToday = $condition;

        return $this;
    }

    public function showAdjacentMonths(bool|Closure $condition = true): static
    {
        $this->showAdjacentMonths = $condition;

        return $this;
    }

    public function hideAdjacentMonths(bool|Closure $condition = true): static
    {
        $this->showAdjacentMonths = is_bool($condition) ? ! $condition : fn (...$args) => ! $this->evaluate($condition, ...$args);

        return $this;
    }

    public function getShowAdjacentMonths(): bool
    {
        return (bool) $this->evaluate($this->showAdjacentMonths);
    }

    public function selectableHeader(bool|Closure $condition = true): static
    {
        $this->selectableHeader = $condition;

        return $this;
    }

    public function getMonths(): int
    {
        return $this->months;
    }

    public function getMode(): ?CalendarMode
    {
        return $this->mode;
    }

    public function getResolvedMode(): CalendarMode
    {
        $mode = $this->getMode();

        if ($mode !== null) {
            return $mode;
        }

        if ($this->isMultiple()) {
            return CalendarMode::Multiple;
        }

        return CalendarMode::Single;
    }

    public function isScrollable(): bool
    {
        return (bool) $this->evaluate($this->scrollable);
    }

    public function getCalendarColumns(): int
    {
        $columns = $this->evaluate($this->calendarColumns);

        if (is_int($columns) && $columns > 0) {
            return min($columns, $this->months);
        }

        return match (true) {
            $this->months <= 2 => 2,
            $this->months <= 6 => 3,
            default => 4,
        };
    }

    public function getSize(): ?string
    {
        return $this->evaluate($this->size);
    }

    public function getWithToday(): bool
    {
        return (bool) $this->evaluate($this->withToday);
    }

    public function getSelectableHeader(): bool
    {
        return (bool) $this->evaluate($this->selectableHeader);
    }

    /**
     * @param  list<string>  $dates
     */
    public function disabledDates(array|Closure $dates): static
    {
        $this->disabledDates = $dates;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getDisabledDates(): array
    {
        return $this->normalizeDateList($this->evaluate($this->disabledDates));
    }

    /**
     * Dates that are taken rather than closed: blocked like unavailable dates,
     * but marked with their own icon so the reason reads differently.
     *
     * @param  list<string>|Closure  $dates
     */
    public function reservedDates(array|Closure $dates): static
    {
        $this->reservedDates = $dates;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getReservedDates(): array
    {
        return $this->normalizeDateList($this->evaluate($this->reservedDates));
    }

    /**
     * Reserved dates usually arrive as Carbon instances plucked off a model,
     * so accept those alongside plain `Y-m-d` strings.
     *
     * @return list<string>
     */
    protected function normalizeDateList(mixed $dates): array
    {
        if (! is_array($dates)) {
            return [];
        }

        $normalized = [];

        foreach ($dates as $date) {
            if ($date instanceof DateTimeInterface) {
                $normalized[] = Carbon::instance($date)->toDateString();

                continue;
            }

            if (! is_string($date) || blank($date)) {
                continue;
            }

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1) {
                $normalized[] = $date;

                continue;
            }

            // ponytail: parse anything else through Carbon; unparseable values are dropped
            // rather than fataling a whole panel render.
            $normalized[] = rescue(fn (): string => Carbon::parse($date)->toDateString(), null, report: false);
        }

        return array_values(array_filter($normalized));
    }

    /**
     * @param  list<string|int>  $days
     */
    public function weekEndDays(array|Closure $days): static
    {
        $this->weekEndDays = $days;

        return $this;
    }

    /**
     * @return list<int>
     */
    public function getWeekEndDays(): array
    {
        $days = $this->evaluate($this->weekEndDays);

        return Weekday::normalizeMany(is_array($days) ? $days : []);
    }

    /**
     * @param  list<string>  $dates
     */
    public function unavailableDates(array|Closure $dates): static
    {
        return $this->disabledDates($dates);
    }

    /**
     * @return list<string>
     */
    public function getUnavailableDates(): array
    {
        return $this->getDisabledDates();
    }
}
