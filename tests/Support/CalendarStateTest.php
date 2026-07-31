<?php

use Asmit\FilaCalendar\Support\CalendarMode;
use Asmit\FilaCalendar\Support\CalendarState;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

describe('empty state', function (): void {
    it('returns the shape each mode expects', function (mixed $empty): void {
        expect(CalendarState::hydrate($empty, CalendarMode::Single))->toBeNull()
            ->and(CalendarState::hydrate($empty, CalendarMode::Range))->toBeNull()
            ->and(CalendarState::hydrate($empty, CalendarMode::Multiple))->toBe([])
            ->and(CalendarState::hydrate($empty, CalendarMode::MultiRange))->toBe([])
            ->and(CalendarState::dehydrate($empty, CalendarMode::Single))->toBeNull()
            ->and(CalendarState::dehydrate($empty, CalendarMode::Range))->toBeNull()
            ->and(CalendarState::dehydrate($empty, CalendarMode::Multiple))->toBe([])
            ->and(CalendarState::dehydrate($empty, CalendarMode::MultiRange))->toBe([]);
    })->with([
        'null' => [null],
        'empty string' => [''],
        'empty array' => [[]],
    ]);

    it('falls back to the multiple flag when no mode is set', function (): void {
        expect(CalendarState::hydrate(null, null, multiple: false))->toBeNull()
            ->and(CalendarState::hydrate(null, null, multiple: true))->toBe([])
            ->and(CalendarState::dehydrate(null, null, multiple: false))->toBeNull()
            ->and(CalendarState::dehydrate(null, null, multiple: true))->toBe([]);
    });

    it('throws on unparseable state rather than falling back to empty', function (mixed $state): void {
        // Current behavior, locked deliberately: hydration has no rescue() the way
        // DateRanges::flatten() does, so a bad stored value surfaces on form load.
        expect(fn () => CalendarState::hydrate($state, CalendarMode::Single))
            ->toThrow(InvalidFormatException::class);
    })->with([
        'zero' => ['0'],
        'not a date' => ['tuesday-ish'],
    ]);

    it('silently rewrites a legacy zero date instead of rejecting it', function (): void {
        expect(CalendarState::hydrate('0000-00-00', CalendarMode::Single))->toBe('-0001-11-30');
    });
});

describe('single mode', function (): void {
    it('normalizes a date string to Y-m-d', function (): void {
        expect(CalendarState::hydrate('2026-08-05 14:30:00', CalendarMode::Single))->toBe('2026-08-05')
            ->and(CalendarState::hydrate('August 5, 2026', CalendarMode::Single))->toBe('2026-08-05');
    });

    it('coerces a Carbon instance', function (): void {
        expect(CalendarState::hydrate(Carbon::parse('2026-08-05 14:30:00'), CalendarMode::Single))->toBe('2026-08-05')
            ->and(CalendarState::dehydrate(Carbon::parse('2026-08-05 14:30:00'), CalendarMode::Single))->toBe('2026-08-05');
    });

    it('unwraps a range-shaped or list-shaped value to its start', function (): void {
        expect(CalendarState::hydrate(['start' => '2026-08-05', 'end' => '2026-08-09'], CalendarMode::Single))->toBe('2026-08-05')
            ->and(CalendarState::hydrate(['2026-08-05', '2026-08-09'], CalendarMode::Single))->toBe('2026-08-05')
            ->and(CalendarState::dehydrate(['start' => Carbon::parse('2026-08-05'), 'end' => '2026-08-09'], CalendarMode::Single))->toBe('2026-08-05');
    });

    it('returns null when the wrapped start is blank', function (): void {
        expect(CalendarState::hydrate(['start' => null, 'end' => '2026-08-09'], CalendarMode::Single))->toBeNull()
            ->and(CalendarState::dehydrate(['start' => '', 'end' => '2026-08-09'], CalendarMode::Single))->toBeNull();
    });

    it('is what an unset mode resolves to when not multiple', function (): void {
        expect(CalendarState::hydrate('2026-08-05 14:30:00', null, multiple: false))->toBe('2026-08-05');
    });

    it('passes a stored string through dehydration untouched', function (): void {
        // dehydrate casts rather than parses: Alpine only ever hands back Y-m-d.
        expect(CalendarState::dehydrate('2026-08-05 14:30:00', CalendarMode::Single))->toBe('2026-08-05 14:30:00');
    });
});

describe('multiple mode', function (): void {
    it('normalizes, dedupes and sorts', function (): void {
        expect(CalendarState::hydrate(['2026-08-09', '2026-08-05 09:00:00', '2026-08-09'], CalendarMode::Multiple))
            ->toBe(['2026-08-05', '2026-08-09']);
    });

    it('drops blank entries', function (): void {
        expect(CalendarState::hydrate(['2026-08-05', null, '', '2026-08-09'], CalendarMode::Multiple))
            ->toBe(['2026-08-05', '2026-08-09'])
            ->and(CalendarState::dehydrate(['2026-08-05', null, '', '2026-08-09'], CalendarMode::Multiple))
            ->toBe(['2026-08-05', '2026-08-09']);
    });

    it('coerces Carbon instances', function (): void {
        expect(CalendarState::hydrate([Carbon::parse('2026-08-09'), Carbon::parse('2026-08-05')], CalendarMode::Multiple))
            ->toBe(['2026-08-05', '2026-08-09'])
            ->and(CalendarState::dehydrate([Carbon::parse('2026-08-09'), Carbon::parse('2026-08-05')], CalendarMode::Multiple))
            ->toBe(['2026-08-05', '2026-08-09']);
    });

    it('rejects range-shaped state instead of stringifying it', function (): void {
        $ranges = [['start' => '2026-08-05', 'end' => '2026-08-09']];

        expect(CalendarState::hydrate($ranges, CalendarMode::Multiple))->toBe([])
            ->and(CalendarState::dehydrate($ranges, CalendarMode::Multiple))->toBe([]);
    });

    it('rejects a scalar', function (): void {
        expect(CalendarState::hydrate('2026-08-05', CalendarMode::Multiple))->toBe([])
            ->and(CalendarState::dehydrate('2026-08-05', CalendarMode::Multiple))->toBe([]);
    });

    it('is what an unset mode resolves to when multiple', function (): void {
        expect(CalendarState::hydrate(['2026-08-09', '2026-08-05'], null, multiple: true))
            ->toBe(['2026-08-05', '2026-08-09']);
    });

    it('reindexes an associative or gappy array', function (): void {
        expect(CalendarState::hydrate(['a' => '2026-08-09', 'b' => '2026-08-05'], CalendarMode::Multiple))
            ->toBe(['2026-08-05', '2026-08-09']);
    });
});

describe('range mode', function (): void {
    it('normalizes both ends', function (): void {
        expect(CalendarState::hydrate(['start' => '2026-08-05 09:00:00', 'end' => '2026-08-09 17:00:00'], CalendarMode::Range))
            ->toBe(['start' => '2026-08-05', 'end' => '2026-08-09']);
    });

    it('accepts a positional pair on hydration', function (): void {
        expect(CalendarState::hydrate(['2026-08-05', '2026-08-09'], CalendarMode::Range))
            ->toBe(['start' => '2026-08-05', 'end' => '2026-08-09']);
    });

    it('coerces Carbon instances', function (): void {
        $range = ['start' => Carbon::parse('2026-08-05 09:00:00'), 'end' => Carbon::parse('2026-08-09')];

        expect(CalendarState::hydrate($range, CalendarMode::Range))->toBe(['start' => '2026-08-05', 'end' => '2026-08-09'])
            ->and(CalendarState::dehydrate($range, CalendarMode::Range))->toBe(['start' => '2026-08-05', 'end' => '2026-08-09']);
    });

    it('returns null when either end is blank', function (): void {
        expect(CalendarState::hydrate(['start' => '2026-08-05', 'end' => null], CalendarMode::Range))->toBeNull()
            ->and(CalendarState::hydrate(['start' => '', 'end' => '2026-08-09'], CalendarMode::Range))->toBeNull()
            ->and(CalendarState::dehydrate(['start' => '2026-08-05', 'end' => ''], CalendarMode::Range))->toBeNull();
    });

    it('returns null for a scalar', function (): void {
        expect(CalendarState::hydrate('2026-08-05', CalendarMode::Range))->toBeNull()
            ->and(CalendarState::dehydrate('2026-08-05', CalendarMode::Range))->toBeNull();
    });

    it('accepts from/until only on dehydration', function (): void {
        // Asymmetry in the current implementation, locked here so a change to it is deliberate.
        $range = ['from' => '2026-08-05', 'until' => '2026-08-09'];

        expect(CalendarState::dehydrate($range, CalendarMode::Range))->toBe(['start' => '2026-08-05', 'end' => '2026-08-09'])
            ->and(CalendarState::hydrate($range, CalendarMode::Range))->toBeNull();
    });
});

describe('multi-range mode', function (): void {
    it('normalizes and sorts ranges by start', function (): void {
        $state = [
            ['start' => '2026-08-17 08:00:00', 'end' => '2026-08-19'],
            ['start' => '2026-08-05', 'end' => '2026-08-09'],
        ];

        expect(CalendarState::hydrate($state, CalendarMode::MultiRange))->toBe([
            ['start' => '2026-08-05', 'end' => '2026-08-09'],
            ['start' => '2026-08-17', 'end' => '2026-08-19'],
        ]);
    });

    it('coerces Carbon instances', function (): void {
        $state = [['start' => Carbon::parse('2026-08-05 09:00:00'), 'end' => Carbon::parse('2026-08-09')]];

        expect(CalendarState::hydrate($state, CalendarMode::MultiRange))->toBe([['start' => '2026-08-05', 'end' => '2026-08-09']])
            ->and(CalendarState::dehydrate($state, CalendarMode::MultiRange))->toBe([['start' => '2026-08-05', 'end' => '2026-08-09']]);
    });

    it('drops incomplete ranges and reindexes', function (): void {
        $state = [
            ['start' => '2026-08-05', 'end' => '2026-08-09'],
            ['start' => '2026-08-17', 'end' => null],
            ['start' => '2026-08-20', 'end' => '2026-08-22'],
        ];

        expect(CalendarState::hydrate($state, CalendarMode::MultiRange))->toBe([
            ['start' => '2026-08-05', 'end' => '2026-08-09'],
            ['start' => '2026-08-20', 'end' => '2026-08-22'],
        ])->and(CalendarState::dehydrate($state, CalendarMode::MultiRange))->toBe([
            ['start' => '2026-08-05', 'end' => '2026-08-09'],
            ['start' => '2026-08-20', 'end' => '2026-08-22'],
        ]);
    });

    it('rejects a flat date list', function (): void {
        expect(CalendarState::hydrate(['2026-08-05', '2026-08-09'], CalendarMode::MultiRange))->toBe([])
            ->and(CalendarState::dehydrate(['2026-08-05', '2026-08-09'], CalendarMode::MultiRange))->toBe([]);
    });

    it('rejects a single range that is not wrapped in a list', function (): void {
        // ['start' => ..., 'end' => ...] fails isRangeList, whose first element is a string.
        expect(CalendarState::hydrate(['start' => '2026-08-05', 'end' => '2026-08-09'], CalendarMode::MultiRange))->toBe([]);
    });

    it('rejects a scalar', function (): void {
        expect(CalendarState::hydrate('2026-08-05', CalendarMode::MultiRange))->toBe([])
            ->and(CalendarState::dehydrate('2026-08-05', CalendarMode::MultiRange))->toBe([]);
    });
});

describe('round trip', function (): void {
    it('survives hydrate then dehydrate unchanged', function (CalendarMode $mode, mixed $state): void {
        $hydrated = CalendarState::hydrate($state, $mode);

        expect(CalendarState::dehydrate($hydrated, $mode))->toBe($hydrated);
    })->with([
        'single' => [CalendarMode::Single, '2026-08-05'],
        'multiple' => [CalendarMode::Multiple, ['2026-08-09', '2026-08-05']],
        'range' => [CalendarMode::Range, ['start' => '2026-08-05', 'end' => '2026-08-09']],
        'multi-range' => [CalendarMode::MultiRange, [['start' => '2026-08-05', 'end' => '2026-08-09']]],
    ]);
});
