<?php

namespace Asmit\FilaCalendar\Concerns;

use Asmit\FilaCalendar\Support\CalendarMode;
use Asmit\FilaCalendar\Support\Locale;
use Asmit\FilaCalendar\Support\Weekday;
use Carbon\Carbon;
use Closure;
use DateTimeInterface;
use Filament\Support\Contracts\ScalableIcon;
use Filament\Support\Icons\Heroicon;

trait HasCalendarConfiguration
{
    /** Tailwind's breakpoint names, narrowest first; the stylesheet matches these widths. */
    protected const CALENDAR_BREAKPOINTS = ['default', 'sm', 'md', 'lg', 'xl', '2xl'];

    protected int $months = 1;

    protected ?CalendarMode $mode = null;

    protected ?string $minDate = null;

    protected ?string $maxDate = null;

    protected bool|Closure|null $scrollable = true;

    /** @var int|array<string, int>|Closure|null */
    protected int|array|Closure|null $calendarColumns = null;

    protected string|Closure|null $size = null;

    protected bool|Closure|null $withToday = null;

    protected bool|Closure $showAdjacentMonths = true;

    protected bool|Closure|null $selectableHeader = null;

    protected bool|Closure $multiple = false;

    /** @var list<string>|Closure */
    protected array|Closure $disabledDates = [];

    /** @var list<string>|Closure */
    protected array|Closure $reservedDates = [];

    protected string|ScalableIcon|Closure $reservedIcon = Heroicon::Bookmark;

    protected string|Closure|null $reservedTooltip = null;

    /** @var list<string|int>|Closure */
    protected array|Closure $weekEndDays = [];

    protected string|Closure|null $locale = null;

    protected Weekday|int|string|Closure|null $weekStartsOn = null;

    public function weekStartsOn(Weekday|int|string|Closure|null $day): static
    {
        $this->weekStartsOn = $day;

        return $this;
    }

    /**
     * The first column of the calendar grid, as an `Intl`-style index (0 = Sunday).
     */
    public function getWeekStartsOn(): int
    {
        return Weekday::normalize($this->evaluate($this->weekStartsOn))
            ?? Weekday::firstDayForLocale($this->getResolvedLocale());
    }

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

    /**
     * A single column count, or one per breakpoint:
     * `->calendarColumns(['sm' => 2, 'lg' => 3])`. Recognised keys are `default`, `sm`, `md`,
     * `lg`, `xl` and `2xl`; each breakpoint holds until the next one overrides it.
     *
     * @param  int|array<string, int>|Closure|null  $columns
     */
    public function calendarColumns(int|array|Closure|null $columns): static
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

    /**
     * Column count per breakpoint, narrowest first. Always holds at least a `default` entry.
     *
     * @return array<string, int>
     */
    public function getResolvedCalendarColumns(): array
    {
        $columns = $this->evaluate($this->calendarColumns);

        if (is_array($columns)) {
            $given = [];

            foreach ($columns as $breakpoint => $value) {
                // 'xxl' and 'xs' are easy to reach for; treat them as their canonical names
                // rather than dropping the entry without a word.
                $breakpoint = match (strtolower((string) $breakpoint)) {
                    'xxl' => '2xl',
                    'xs' => 'default',
                    default => strtolower((string) $breakpoint),
                };

                $given[$breakpoint] = $value;
            }

            $resolved = [];

            foreach (self::CALENDAR_BREAKPOINTS as $breakpoint) {
                if (! array_key_exists($breakpoint, $given) || ! is_numeric($given[$breakpoint])) {
                    continue;
                }

                $resolved[$breakpoint] = $this->clampCalendarColumns((int) $given[$breakpoint]);
            }

            if ($resolved !== []) {
                return $resolved;
            }
        }

        if (is_numeric($columns) && (int) $columns > 0) {
            return ['default' => $this->clampCalendarColumns((int) $columns)];
        }

        return ['default' => match (true) {
            $this->months <= 2 => 2,
            $this->months <= 6 => 3,
            default => 4,
        }];
    }

    /**
     * The narrowest configured column count, kept for callers that want a single number.
     */
    public function getCalendarColumns(): int
    {
        $resolved = $this->getResolvedCalendarColumns();

        return $resolved['default'] ?? reset($resolved);
    }

    /**
     * Inline custom properties the stylesheet reads at each breakpoint.
     */
    public function getCalendarColumnsStyle(): string
    {
        $declarations = [];

        foreach ($this->getResolvedCalendarColumns() as $breakpoint => $columns) {
            $property = $breakpoint === 'default'
                ? '--fi-calendar-columns'
                : "--fi-calendar-columns-{$breakpoint}";

            $declarations[] = "{$property}: {$columns}";
        }

        return implode('; ', $declarations);
    }

    protected function clampCalendarColumns(int $columns): int
    {
        return max(1, min($columns, $this->months));
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

    public function reservedIcon(string|ScalableIcon|Closure $icon): static
    {
        $this->reservedIcon = $icon;

        return $this;
    }

    public function getReservedIcon(): string|ScalableIcon
    {
        $icon = $this->evaluate($this->reservedIcon);

        if ($icon instanceof ScalableIcon) {
            return $icon;
        }

        return is_string($icon) && filled($icon) ? $icon : Heroicon::Bookmark;
    }

    /**
     * Tooltip for reserved days. Defaults to the translated "Reserved" label; pass an empty
     * string to drop the tooltip while keeping the icon.
     */
    public function reservedTooltip(string|Closure|null $tooltip): static
    {
        $this->reservedTooltip = $tooltip;

        return $this;
    }

    public function getReservedTooltip(): ?string
    {
        $tooltip = $this->evaluate($this->reservedTooltip);

        if ($tooltip === null) {
            return __('fila-calendar::calendar.reserved');
        }

        return filled($tooltip) ? $tooltip : null;
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
