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

console.log('ok')
