export default function filamentCalendar(config) {
    const parseDate = (value) => {
        if (value instanceof Date) {
            return new Date(value.getFullYear(), value.getMonth(), value.getDate())
        }

        const [year, month, day] = String(value).split('-').map(Number)

        return new Date(year, month - 1, day)
    }

    const toDateString = (date) => {
        const year = date.getFullYear()
        const month = String(date.getMonth() + 1).padStart(2, '0')
        const day = String(date.getDate()).padStart(2, '0')

        return `${year}-${month}-${day}`
    }

    const addMonths = (date, count) => new Date(date.getFullYear(), date.getMonth() + count, 1)

    const daysInMonth = (year, month) => new Date(year, month + 1, 0).getDate()

    const normalizeRange = (range) => {
        if (! range?.start) {
            return null
        }

        let start = range.start
        let end = range.end ?? range.start

        if (end < start) {
            ;[start, end] = [end, start]
        }

        return { start, end }
    }

    const resolveInitialAnchorDate = () => {
        if (config.mode === 'single' && config.state) {
            return parseDate(config.state)
        }

        if (config.mode === 'multiple' && Array.isArray(config.state) && config.state.length > 0) {
            return parseDate(config.state[0])
        }

        if (config.mode === 'range' && config.state?.start) {
            return parseDate(config.state.start)
        }

        if (config.mode === 'multi-range' && Array.isArray(config.state) && config.state.length > 0) {
            return parseDate(config.state[0].start)
        }

        if (config.initialDate) {
            return parseDate(config.initialDate)
        }

        return new Date()
    }

    const normalizeDateList = (dates) => {
        if (! Array.isArray(dates)) {
            return []
        }

        return dates.map((date) => {
            if (typeof date === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(date)) {
                return date
            }

            return toDateString(parseDate(date))
        })
    }

    const addDays = (dateString, count) => {
        const date = parseDate(dateString)
        date.setDate(date.getDate() + count)

        return toDateString(date)
    }

    const resolveInitialState = () => {
        if (config.readOnly && config.mode === 'multi-range') {
            return Array.isArray(config.state) ? config.state : []
        }

        return config.state
    }

    const anchor = resolveInitialAnchorDate()

    return {
        state: resolveInitialState(),
        pendingRange: null,
        hoveredDate: null,
        hoverRevision: 0,
        mode: config.mode ?? 'single',
        minDate: config.minDate ?? null,
        maxDate: config.maxDate ?? null,
        unavailableDates: normalizeDateList(config.unavailableDates ?? config.disabledDates ?? []),
        disabledDates: normalizeDateList(config.disabledDates ?? config.unavailableDates ?? []),
        weekEndDays: config.weekEndDays ?? [],
        months: config.months ?? 1,
        selectableHeader: config.selectableHeader ?? false,
        withToday: config.withToday ?? false,
        readOnly: config.readOnly ?? false,
        disabled: config.disabled ?? false,
        viewStart: new Date(anchor.getFullYear(), anchor.getMonth(), 1),
        weekdayLabels: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],

        init() {
            this.$watch('hoveredDate', () => {
                this.hoverRevision += 1
            })

            this.$watch('pendingRange', () => {
                this.hoverRevision += 1
            })
        },

        monthsToRender() {
            return Array.from({ length: this.months }, (_, index) => addMonths(this.viewStart, index))
        },

        monthLabel(date) {
            return date.toLocaleDateString(undefined, { month: 'long', year: 'numeric' })
        },

        toolbarLabel() {
            const months = this.monthsToRender()

            if (months.length === 0) {
                return ''
            }

            const first = this.monthLabel(months[0])
            const last = this.monthLabel(months[months.length - 1])

            return first === last ? first : `${first} – ${last}`
        },

        calendarDays(monthDate) {
            const year = monthDate.getFullYear()
            const month = monthDate.getMonth()
            const firstDay = new Date(year, month, 1)
            const startOffset = firstDay.getDay()
            const totalDays = daysInMonth(year, month)
            const days = []
            const previousMonth = addMonths(monthDate, -1)
            const previousMonthDays = daysInMonth(previousMonth.getFullYear(), previousMonth.getMonth())

            for (let index = startOffset - 1; index >= 0; index -= 1) {
                const day = previousMonthDays - index
                const date = new Date(previousMonth.getFullYear(), previousMonth.getMonth(), day)
                days.push({ date: toDateString(date), day, currentMonth: false })
            }

            for (let day = 1; day <= totalDays; day += 1) {
                const date = new Date(year, month, day)
                days.push({ date: toDateString(date), day, currentMonth: true })
            }

            const nextMonth = addMonths(monthDate, 1)
            let trailingDay = 1

            while (days.length % 7 !== 0) {
                const date = new Date(nextMonth.getFullYear(), nextMonth.getMonth(), trailingDay)
                days.push({ date: toDateString(date), day: trailingDay, currentMonth: false })
                trailingDay += 1
            }

            return days
        },

        getRanges() {
            if (this.mode !== 'multi-range' || ! Array.isArray(this.state)) {
                return []
            }

            return this.state
                .map((range) => normalizeRange(range))
                .filter((range) => range !== null)
        },

        findRangeIndexForDate(date) {
            return this.getRanges().findIndex((range) => {
                return date >= range.start && date <= range.end
            })
        },

        getRangeRole(date) {
            if (this.mode === 'multi-range') {
                if (this.pendingRange?.start === date && ! this.pendingRange?.end) {
                    return 'pending-start'
                }

                for (const range of this.getRanges()) {
                    if (date === range.start && date === range.end) {
                        return 'start-end'
                    }

                    if (date === range.start) {
                        return 'start'
                    }

                    if (date === range.end) {
                        return 'end'
                    }

                    if (date > range.start && date < range.end) {
                        return 'middle'
                    }
                }

                return null
            }

            if (this.mode === 'range') {
                if (! this.state?.start) {
                    return null
                }

                if (! this.state?.end) {
                    if (date === this.state.start) {
                        return 'pending-start'
                    }

                    return null
                }

                const end = this.state.end

                if (date === this.state.start && date === end) {
                    return 'start-end'
                }

                if (date === this.state.start) {
                    return 'start'
                }

                if (date === this.state.end) {
                    return 'end'
                }

                if (date > this.state.start && date < end) {
                    return 'middle'
                }
            }

            return null
        },

        isToday(date) {
            return date === toDateString(new Date())
        },

        isUnavailableDate(date) {
            return this.unavailableDates.includes(date)
        },

        isWeekEndDay(date) {
            return this.weekEndDays.includes(parseDate(date).getDay())
        },

        isOutOfBounds(date) {
            if (this.minDate && date < this.minDate) {
                return true
            }

            if (this.maxDate && date > this.maxDate) {
                return true
            }

            return false
        },

        isButtonDisabled(date) {
            return this.disabled || this.isOutOfBounds(date)
        },

        isInteractionBlocked(date) {
            return this.isButtonDisabled(date) || this.isUnavailableDate(date) || this.isWeekEndDay(date)
        },

        isDisabled(date) {
            return this.isInteractionBlocked(date)
        },

        splitRangeAroundBlockedDates(start, end) {
            let rangeStart = start
            let rangeEnd = end

            if (rangeEnd < rangeStart) {
                ;[rangeStart, rangeEnd] = [rangeEnd, rangeStart]
            }

            const ranges = []
            let currentStart = null
            let currentEnd = null
            let current = rangeStart

            while (current <= rangeEnd) {
                if (this.isInteractionBlocked(current)) {
                    if (currentStart !== null) {
                        ranges.push({ start: currentStart, end: currentEnd })
                        currentStart = null
                        currentEnd = null
                    }
                } else if (currentStart === null) {
                    currentStart = current
                    currentEnd = current
                } else {
                    currentEnd = current
                }

                if (current === rangeEnd) {
                    break
                }

                current = addDays(current, 1)
            }

            if (currentStart !== null) {
                ranges.push({ start: currentStart, end: currentEnd })
            }

            return ranges
        },

        splitRangeExcludingMiddleDate(range, date) {
            const start = range.start
            const end = range.end

            if (date <= start || date >= end) {
                return []
            }

            const ranges = []
            const dayBefore = addDays(date, -1)

            if (dayBefore >= start) {
                ranges.push({ start, end: dayBefore })
            }

            const dayAfter = addDays(date, 1)

            if (dayAfter <= end) {
                ranges.push({ start: dayAfter, end })
            }

            return ranges
        },

        getPendingSelectionStart() {
            if (this.mode === 'multi-range' && this.pendingRange?.start && ! this.pendingRange?.end) {
                return this.pendingRange.start
            }

            if (this.mode === 'range' && this.state?.start && ! this.state?.end) {
                return this.state.start
            }

            return null
        },

        setHoveredDate(date) {
            if (this.readOnly || this.disabled || this.isInteractionBlocked(date)) {
                return
            }

            if (this.getPendingSelectionStart() !== null) {
                this.hoveredDate = date
            }
        },

        clearHoveredDate() {
            this.hoveredDate = null
        },

        getPendingHoverSegments() {
            const start = this.getPendingSelectionStart()

            if (start === null || this.hoveredDate === null) {
                return []
            }

            return this.splitRangeAroundBlockedDates(start, this.hoveredDate)
        },

        getPendingHoverRole(date) {
            const start = this.getPendingSelectionStart()

            if (start === null || this.hoveredDate === null || this.isInteractionBlocked(date)) {
                return null
            }

            if (this.getRangeRole(date) !== null) {
                return null
            }

            for (const segment of this.getPendingHoverSegments()) {
                if (date === segment.start && date === segment.end) {
                    return 'preview'
                }

                if (date === segment.start || date === segment.end) {
                    return 'preview'
                }

                if (date > segment.start && date < segment.end) {
                    return 'preview'
                }
            }

            return null
        },

        isSelected(date) {
            if (! this.state) {
                return false
            }

            if (this.mode === 'single') {
                return this.state === date
            }

            if (this.mode === 'multiple') {
                return Array.isArray(this.state) && this.state.includes(date)
            }

            if (this.mode === 'range' || this.mode === 'multi-range') {
                return this.getRangeRole(date) !== null
            }

            return false
        },

        dayClasses(day, revision = 0) {
            void revision

            const classes = ['fi-calendar-day']

            if (! day.currentMonth) {
                classes.push('fi-calendar-day--outside')
            }

            if (this.isToday(day.date)) {
                classes.push('fi-calendar-day--today')
            }

            if (this.isUnavailableDate(day.date)) {
                classes.push('fi-calendar-day--unavailable')

                return classes.join(' ')
            }

            if (this.isWeekEndDay(day.date)) {
                classes.push('fi-calendar-day--week-end')

                return classes.join(' ')
            }

            if (this.isOutOfBounds(day.date)) {
                classes.push('fi-calendar-day--disabled')

                return classes.join(' ')
            }

            const role = this.getRangeRole(day.date)

            if (role === 'pending-start') {
                classes.push('fi-calendar-day--range-start', 'fi-calendar-day--range-pending', 'fi-calendar-day--selected')
            }

            if (role === 'start' || role === 'start-end') {
                classes.push('fi-calendar-day--range-start')
            }

            if (role === 'end' || role === 'start-end') {
                classes.push('fi-calendar-day--range-end')
            }

            if (role === 'middle') {
                classes.push('fi-calendar-day--in-range')
            }

            const hoverRole = this.getPendingHoverRole(day.date)

            if (hoverRole === 'preview') {
                classes.push('fi-calendar-day--in-range-hover')
            }

            if (this.mode === 'single' && this.state === day.date) {
                classes.push('fi-calendar-day--selected')
            }

            if (this.mode === 'multiple' && Array.isArray(this.state) && this.state.includes(day.date)) {
                classes.push('fi-calendar-day--selected')
            }

            return classes.join(' ')
        },

        selectDate(date) {
            if (this.readOnly || this.isInteractionBlocked(date)) {
                return
            }

            if (this.mode === 'single') {
                this.state = this.state === date ? null : date

                return
            }

            if (this.mode === 'multiple') {
                const dates = Array.isArray(this.state) ? [...this.state] : []
                const index = dates.indexOf(date)

                if (index >= 0) {
                    dates.splice(index, 1)
                } else {
                    dates.push(date)
                }

                dates.sort()
                this.state = dates

                return
            }

            if (this.mode === 'range') {
                if (! this.state?.start || (this.state.start && this.state.end)) {
                    this.state = { start: date, end: null }
                    this.hoveredDate = null

                    return
                }

                let start = this.state.start
                let end = date

                if (end < start) {
                    ;[start, end] = [end, start]
                }

                const segments = this.splitRangeAroundBlockedDates(start, end)

                this.state = segments[0] ?? null
                this.hoveredDate = null

                return
            }

            if (this.mode === 'multi-range') {
                const existingRangeIndex = this.findRangeIndexForDate(date)

                if (existingRangeIndex >= 0) {
                    const range = this.getRanges()[existingRangeIndex]
                    const role = this.getRangeRole(date)
                    const otherRanges = this.getRanges().filter((_, index) => index !== existingRangeIndex)
                    let replacementRanges = []

                    if (role === 'middle') {
                        replacementRanges = this.splitRangeExcludingMiddleDate(range, date)
                    }

                    this.state = [
                        ...otherRanges,
                        ...replacementRanges,
                    ].sort((left, right) => left.start.localeCompare(right.start))

                    this.pendingRange = null
                    this.hoveredDate = null

                    return
                }

                if (! this.pendingRange?.start || this.pendingRange.end) {
                    this.pendingRange = { start: date, end: null }
                    this.hoveredDate = null

                    return
                }

                let start = this.pendingRange.start
                let end = date

                if (end < start) {
                    ;[start, end] = [end, start]
                }

                const newRanges = this.splitRangeAroundBlockedDates(start, end)

                if (newRanges.length === 0) {
                    this.pendingRange = null
                    this.hoveredDate = null

                    return
                }

                this.state = [
                    ...this.getRanges(),
                    ...newRanges,
                ].sort((left, right) => left.start.localeCompare(right.start))

                this.pendingRange = null
                this.hoveredDate = null
            }
        },

        previousMonth() {
            this.viewStart = addMonths(this.viewStart, -1)
        },

        nextMonth() {
            this.viewStart = addMonths(this.viewStart, 1)
        },

        goToToday() {
            const today = new Date()
            this.viewStart = new Date(today.getFullYear(), today.getMonth(), 1)
        },

        setMonth(monthIndex, month) {
            const target = addMonths(this.viewStart, monthIndex)
            const updated = new Date(target.getFullYear(), Number(month), 1)

            this.viewStart = addMonths(updated, -monthIndex)
        },

        setYear(monthIndex, year) {
            const target = addMonths(this.viewStart, monthIndex)
            const updated = new Date(Number(year), target.getMonth(), 1)

            this.viewStart = addMonths(updated, -monthIndex)
        },
    }
}
