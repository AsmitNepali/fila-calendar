// Run: node resources/js/fila-calendar.test.mjs
import assert from 'node:assert/strict'

import filaCalendar from './fila-calendar.js'

const multiRange = (state = []) => filaCalendar({ mode: 'multi-range', state })

// A range drawn across an existing one is merged instead of overlapping it.
{
    const calendar = multiRange()

    calendar.selectDate('2026-08-10')
    calendar.selectDate('2026-08-12')
    calendar.selectDate('2026-08-05')
    calendar.selectDate('2026-08-20')

    assert.deepEqual(calendar.state, [{ start: '2026-08-05', end: '2026-08-20' }])
}

// One click on a middle date deselects it and closes the neighbouring ranges.
{
    const calendar = multiRange([{ start: '2026-08-05', end: '2026-08-20' }])

    calendar.selectDate('2026-08-11')

    assert.equal(calendar.isSelected('2026-08-11'), false)
    assert.deepEqual(calendar.state, [
        { start: '2026-08-05', end: '2026-08-10' },
        { start: '2026-08-12', end: '2026-08-20' },
    ])
}

// Overlapping ranges hydrated from the server still deselect in a single click.
{
    const calendar = multiRange([
        { start: '2026-08-05', end: '2026-08-20' },
        { start: '2026-08-10', end: '2026-08-12' },
    ])

    calendar.selectDate('2026-08-11')

    assert.equal(calendar.isSelected('2026-08-11'), false)
}

// Clicking a range edge shrinks the range instead of dropping it.
{
    const calendar = multiRange([{ start: '2026-08-05', end: '2026-08-20' }])

    calendar.selectDate('2026-08-05')
    assert.deepEqual(calendar.state, [{ start: '2026-08-06', end: '2026-08-20' }])

    calendar.selectDate('2026-08-20')
    assert.deepEqual(calendar.state, [{ start: '2026-08-06', end: '2026-08-19' }])
}

// Clicking a single day range clears it.
{
    const calendar = multiRange([{ start: '2026-08-05', end: '2026-08-05' }])

    calendar.selectDate('2026-08-05')

    assert.deepEqual(calendar.state, [])
}

// Shift-click clears the whole range.
{
    const calendar = multiRange([
        { start: '2026-08-05', end: '2026-08-20' },
        { start: '2026-09-01', end: '2026-09-03' },
    ])

    calendar.selectDate('2026-08-11', { shiftKey: true })

    assert.deepEqual(calendar.state, [{ start: '2026-09-01', end: '2026-09-03' }])
}

// Touching ranges stay separate.
{
    const calendar = multiRange([
        { start: '2026-08-05', end: '2026-08-10' },
        { start: '2026-08-11', end: '2026-08-20' },
    ])

    assert.equal(calendar.getRanges().length, 2)
}

// Hidden adjacent months keep the grid at seven columns with inert placeholders.
{
    const shown = filaCalendar({ mode: 'single', initialDate: '2026-08-01' })
    const hidden = filaCalendar({ mode: 'single', initialDate: '2026-08-01', showAdjacentMonths: false })
    const month = new Date(2026, 7, 1)

    const shownDays = shown.calendarDays(month)
    const hiddenDays = hidden.calendarDays(month)

    assert.equal(hiddenDays.length, shownDays.length)
    assert.equal(hiddenDays.length % 7, 0)
    assert.equal(hiddenDays.filter((day) => day.placeholder).length, shownDays.filter((day) => ! day.date?.startsWith('2026-08')).length)
    assert.ok(hiddenDays.filter((day) => ! day.placeholder).every((day) => day.date.startsWith('2026-08')))
    assert.ok(hiddenDays.filter((day) => day.placeholder).every((day) => day.date === null && day.day === null))
}

// A range spans blocked days instead of splitting around them.
{
    const calendar = filaCalendar({
        mode: 'multi-range',
        state: [],
        disabledDates: ['2026-08-08'],
        unavailableDates: ['2026-08-08'],
    })

    calendar.selectDate('2026-08-05')
    calendar.selectDate('2026-08-12')

    assert.deepEqual(calendar.state, [{ start: '2026-08-05', end: '2026-08-12' }])
    assert.equal(calendar.isSelected('2026-08-08'), true)
}

// A blocked day inside a range keeps the range highlight and stays unclickable.
{
    const calendar = filaCalendar({
        mode: 'multi-range',
        state: [{ start: '2026-08-05', end: '2026-08-12' }],
        disabledDates: ['2026-08-08'],
        unavailableDates: ['2026-08-08'],
    })

    const classes = calendar.dayClasses({ date: '2026-08-08', day: 8, currentMonth: true, placeholder: false })

    assert.ok(classes.includes('fi-calendar-day--in-range'))
    assert.ok(classes.includes('fi-calendar-day--unavailable'))

    calendar.selectDate('2026-08-08')

    assert.deepEqual(calendar.state, [{ start: '2026-08-05', end: '2026-08-12' }])
}

// Shift-click keeps the blocked days a range has to cover.
{
    const blocked = ['2026-08-06', '2026-08-09', '2026-08-10']
    const calendar = filaCalendar({
        mode: 'multi-range',
        state: [{ start: '2026-08-05', end: '2026-08-10' }],
        disabledDates: blocked,
        unavailableDates: blocked,
    })

    calendar.selectDate('2026-08-07', { shiftKey: true })

    assert.deepEqual(calendar.state, [
        { start: '2026-08-06', end: '2026-08-06' },
        { start: '2026-08-09', end: '2026-08-10' },
    ])
}

// Shift-click still clears a range with nothing blocked inside it.
{
    const calendar = multiRange([{ start: '2026-08-05', end: '2026-08-10' }])

    calendar.selectDate('2026-08-07', { shiftKey: true })

    assert.deepEqual(calendar.state, [])
}

// A reserved day is blocked, and cannot start a range.
{
    const calendar = filaCalendar({ mode: 'multi-range', state: [], reservedDates: ['2026-08-11'] })

    assert.equal(calendar.isReservedDate('2026-08-11'), true)
    assert.equal(calendar.isInteractionBlocked('2026-08-11'), true)

    calendar.selectDate('2026-08-11')

    assert.deepEqual(calendar.state, [])
}

// A range spanning a reserved day keeps the reserved marker, so the conflict stays visible.
{
    const calendar = filaCalendar({
        mode: 'multi-range',
        state: [{ start: '2026-08-10', end: '2026-08-12' }],
        reservedDates: ['2026-08-11'],
    })

    const classes = calendar.dayClasses({ date: '2026-08-11', day: 11, currentMonth: true, placeholder: false })

    assert.ok(classes.includes('fi-calendar-day--in-range'))
    assert.ok(classes.includes('fi-calendar-day--reserved'))
}

// Reserved beats unavailable when a date is in both lists.
{
    const calendar = filaCalendar({ mode: 'single', reservedDates: ['2026-08-11'], unavailableDates: ['2026-08-11'] })

    const classes = calendar.dayClasses({ date: '2026-08-11', day: 11, currentMonth: true, placeholder: false })

    assert.ok(classes.includes('fi-calendar-day--reserved'))
    assert.ok(! classes.includes('fi-calendar-day--unavailable'))
}

// Carbon-style datetime strings from the server still match a plain Y-m-d day.
{
    const calendar = filaCalendar({ mode: 'single', reservedDates: ['2026-08-11 14:30:00'] })

    assert.equal(calendar.isReservedDate('2026-08-11'), true)
}

// A range drawn over reserved days splits around them instead of booking them twice.
{
    const calendar = filaCalendar({
        mode: 'multi-range',
        state: [],
        reservedDates: ['2026-08-12', '2026-08-13', '2026-08-17'],
    })

    calendar.selectDate('2026-08-11')
    calendar.selectDate('2026-08-19')

    assert.deepEqual(calendar.state, [
        { start: '2026-08-11', end: '2026-08-11' },
        { start: '2026-08-14', end: '2026-08-16' },
        { start: '2026-08-18', end: '2026-08-19' },
    ])

    // None of the reserved days ended up selected.
    assert.equal(calendar.isSelected('2026-08-12'), false)
    assert.equal(calendar.isSelected('2026-08-13'), false)
    assert.equal(calendar.isSelected('2026-08-17'), false)
}

// Dragging backwards over a booking splits the same way.
{
    const calendar = filaCalendar({ mode: 'multi-range', state: [], reservedDates: ['2026-08-15'] })

    calendar.selectDate('2026-08-18')
    calendar.selectDate('2026-08-13')

    assert.deepEqual(calendar.state, [
        { start: '2026-08-13', end: '2026-08-14' },
        { start: '2026-08-16', end: '2026-08-18' },
    ])
}

// Unavailable and weekend days are still spanned; only reserved days break a range.
{
    const calendar = filaCalendar({
        mode: 'multi-range',
        state: [],
        unavailableDates: ['2026-08-12'],
        weekEndDays: [0, 6],
    })

    calendar.selectDate('2026-08-11')
    calendar.selectDate('2026-08-14')

    assert.deepEqual(calendar.state, [{ start: '2026-08-11', end: '2026-08-14' }])
}

// The drag preview shows the same gaps, so the split is visible before committing.
{
    const calendar = filaCalendar({ mode: 'multi-range', state: [], reservedDates: ['2026-08-13'] })

    calendar.selectDate('2026-08-11')
    calendar.setHoveredDate('2026-08-15')

    const classesFor = (date) => calendar.dayClasses({ date, day: Number(date.slice(-2)), currentMonth: true, placeholder: false })

    assert.ok(classesFor('2026-08-12').includes('fi-calendar-day--in-range-hover'))
    assert.ok(! classesFor('2026-08-13').includes('fi-calendar-day--in-range-hover'))
    assert.ok(classesFor('2026-08-14').includes('fi-calendar-day--in-range-hover'))
}

// A single range cannot hold a gap, so it stops before the booking it ran into.
{
    const calendar = filaCalendar({ mode: 'range', state: null, reservedDates: ['2026-08-14'] })

    calendar.selectDate('2026-08-11')
    calendar.selectDate('2026-08-19')

    assert.deepEqual(calendar.state, { start: '2026-08-11', end: '2026-08-13' })
}

// Range mode dragging backwards keeps the run touching the day it started from.
{
    const calendar = filaCalendar({ mode: 'range', state: null, reservedDates: ['2026-08-14'] })

    calendar.selectDate('2026-08-18')
    calendar.selectDate('2026-08-11')

    assert.deepEqual(calendar.state, { start: '2026-08-15', end: '2026-08-18' })
}

const keyboard = (config = {}) => filaCalendar({
    state: null,
    mode: 'single',
    locale: 'en-US',
    initialDate: '2026-03-15',
    ...config,
})

// Weeks start on Sunday unless a locale-derived week start says otherwise.
{
    const calendar = keyboard()
    const days = calendar.calendarDays(new Date(2026, 2, 1))

    assert.ok(calendar.weekdayLabels[0].startsWith('Su'))
    assert.equal(days[0].date, '2026-03-01')
    assert.equal(days[0].currentMonth, true)
}

// 2026-03-01 is a Sunday, so a Monday-first grid shows the whole previous week.
{
    const calendar = keyboard({ weekStartsOn: 1 })
    const days = calendar.calendarDays(new Date(2026, 2, 1))

    assert.ok(calendar.weekdayLabels[0].startsWith('Mo'))
    assert.ok(calendar.weekdayLabels[6].startsWith('Su'))
    assert.equal(days[0].date, '2026-02-23')
    assert.equal(days[6].date, '2026-03-01')
}

// A Saturday-first locale leads with the previous Saturday.
{
    assert.equal(
        keyboard({ weekStartsOn: 6, locale: 'ar' }).calendarDays(new Date(2026, 2, 1))[0].date,
        '2026-02-28',
    )
}

// Every rendered week holds seven cells, whatever the week start.
{
    const weeks = keyboard({ weekStartsOn: 1 }).calendarWeeks(new Date(2026, 1, 1))

    assert.ok(weeks.length >= 4)
    weeks.forEach((week) => assert.equal(week.length, 7))
}

// Arrow keys move by day and by week; unhandled keys are left alone.
{
    const calendar = keyboard()

    assert.equal(calendar.dateForKey('ArrowRight', '2026-03-15'), '2026-03-16')
    assert.equal(calendar.dateForKey('ArrowLeft', '2026-03-15'), '2026-03-14')
    assert.equal(calendar.dateForKey('ArrowDown', '2026-03-15'), '2026-03-22')
    assert.equal(calendar.dateForKey('ArrowUp', '2026-03-15'), '2026-03-08')
    assert.equal(calendar.dateForKey('Backspace', '2026-03-15'), null)
}

// The inline axis follows the panel direction.
{
    const calendar = keyboard()
    calendar.rtl = true

    assert.equal(calendar.dateForKey('ArrowRight', '2026-03-15'), '2026-03-14')
    assert.equal(calendar.dateForKey('ArrowLeft', '2026-03-15'), '2026-03-16')
    assert.equal(calendar.dateForKey('ArrowDown', '2026-03-15'), '2026-03-22')
}

// Home and End land on the week edges, which move with the week start.
{
    const sundayFirst = keyboard()
    const mondayFirst = keyboard({ weekStartsOn: 1 })

    assert.equal(sundayFirst.dateForKey('Home', '2026-03-18'), '2026-03-15')
    assert.equal(sundayFirst.dateForKey('End', '2026-03-18'), '2026-03-21')
    assert.equal(mondayFirst.dateForKey('Home', '2026-03-18'), '2026-03-16')
    assert.equal(mondayFirst.dateForKey('End', '2026-03-18'), '2026-03-22')
}

// Page keys move by month, or by year with shift, clamping in shorter months.
{
    const calendar = keyboard()

    assert.equal(calendar.dateForKey('PageUp', '2026-03-15'), '2026-02-15')
    assert.equal(calendar.dateForKey('PageDown', '2026-03-15'), '2026-04-15')
    assert.equal(calendar.dateForKey('PageUp', '2026-03-15', true), '2025-03-15')
    assert.equal(calendar.dateForKey('PageDown', '2026-03-15', true), '2027-03-15')
    assert.equal(calendar.dateForKey('PageUp', '2026-03-31'), '2026-02-28')
}

// Focus is clamped to the allowed bounds, and a disabled calendar takes none.
{
    const calendar = keyboard({ minDate: '2026-03-10', maxDate: '2026-03-20' })

    assert.equal(calendar.clampToBounds('2026-03-01'), '2026-03-10')
    assert.equal(calendar.clampToBounds('2026-03-25'), '2026-03-20')
    assert.equal(calendar.clampToBounds('2026-03-15'), '2026-03-15')
    assert.equal(keyboard({ disabled: true }).clampToBounds('2026-03-15'), null)
}

// Blocked days cannot hold focus, so travel continues past them.
{
    const calendar = keyboard({ disabledDates: ['2026-03-16', '2026-03-17'] })

    assert.equal(calendar.nextFocusableDate('2026-03-16', 1), '2026-03-18')
    assert.equal(calendar.nextFocusableDate('2026-03-17', -1), '2026-03-15')
    assert.equal(calendar.focusStepForKey('ArrowUp'), -1)
    assert.equal(calendar.focusStepForKey('ArrowRight'), 1)

    // Nothing focusable before the minimum, so the walk gives up rather than looping.
    const walled = keyboard({ minDate: '2026-03-16', disabledDates: ['2026-03-16'] })
    assert.equal(walled.nextFocusableDate('2026-03-16', -1), null)
}

// The grid keeps one tab stop, wherever the focused day is rendered.
{
    const calendar = keyboard({ months: 2 })
    calendar.focusedDate = '2026-03-15'

    const tabStops = calendar
        .monthsToRender()
        .flatMap((month) => calendar.calendarDays(month))
        .filter((day) => calendar.dayTabIndex(day) === 0)

    assert.equal(tabStops.length, 1)
    assert.equal(tabStops[0].date, '2026-03-15')
    assert.equal(calendar.clampToView('2027-01-01'), '2026-03-01')
}

// The view scrolls only as far as it must to reveal a newly focused date.
{
    const calendar = keyboard({ months: 2 })

    calendar.ensureDateIsVisible('2026-05-04')
    assert.equal(calendar.viewStart.getMonth(), 3)

    calendar.ensureDateIsVisible('2026-01-04')
    assert.equal(calendar.viewStart.getMonth(), 0)
}

// Selections are announced with the localised date.
{
    const calendar = keyboard({
        i18n: { selected: ':date selected.', deselected: ':date deselected.' },
    })

    calendar.selectDate('2026-03-15')
    assert.match(calendar.announcement, /March 15, 2026 selected\./)

    calendar.selectDate('2026-03-15')
    assert.match(calendar.announcement, /March 15, 2026 deselected\./)
}

// Shift + arrows open a range from the focused day and preview it; Enter commits.
{
    const calendar = keyboard({ mode: 'range', i18n: {} })

    calendar.extendSelectionTo('2026-03-18', '2026-03-15')
    assert.deepEqual(calendar.state, { start: '2026-03-15', end: null })
    assert.equal(calendar.hoveredDate, '2026-03-18')

    calendar.selectDate('2026-03-18')
    assert.deepEqual(calendar.state, { start: '2026-03-15', end: '2026-03-18' })
}

console.log('ok')
