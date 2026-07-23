<script setup lang="ts">
const weekdays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']

const months = [
  {
    label: 'July 2026',
    days: buildMonth(2026, 6, [
      { start: 2, end: 8 },
      { start: 14, end: 18 }
    ])
  },
  {
    label: 'August 2026',
    days: buildMonth(2026, 7, [
      { start: 4, end: 11 },
      { start: 22, end: 26 }
    ])
  },
  {
    label: 'September 2026',
    days: buildMonth(2026, 8, [
      { start: 1, end: 5 }
    ])
  }
]

function buildMonth(year: number, month: number, ranges: Array<{ start: number, end: number }>) {
  const firstDay = new Date(year, month, 1).getDay()
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const cells: Array<{ day: number | null, state?: string }> = []

  for (let i = 0; i < firstDay; i++) {
    cells.push({ day: null })
  }

  for (let day = 1; day <= daysInMonth; day++) {
    let state = 'default'

    for (const range of ranges) {
      if (day === range.start && day === range.end) {
        state = 'single'
      } else if (day === range.start) {
        state = 'start'
      } else if (day === range.end) {
        state = 'end'
      } else if (day > range.start && day < range.end) {
        state = 'middle'
      }
    }

    cells.push({ day, state })
  }

  return cells
}

function dayClass(state?: string): string {
  switch (state) {
    case 'single':
      return 'bg-amber-500/90 text-white shadow-sm shadow-amber-500/30'
    case 'start':
      return 'bg-amber-500/90 text-white rounded-l-full'
    case 'end':
      return 'bg-amber-500/90 text-white rounded-r-full'
    case 'middle':
      return 'bg-amber-500/25 text-amber-100'
    default:
      return 'text-zinc-400 hover:bg-white/5'
  }
}
</script>

<template>
  <div class="glass-container mx-auto w-full max-w-5xl">
    <div
      class="pointer-events-none absolute left-1/3 top-0 -z-10 h-2/3 w-2/3 bg-amber-500/40 mix-blend-screen opacity-80 blur-3xl filter md:blur-[120px] dark:bg-amber-600/50"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute left-0 top-1/3 -z-10 h-2/3 w-2/3 bg-orange-500/30 mix-blend-screen opacity-80 blur-3xl filter md:blur-[120px] dark:bg-orange-600/40"
      aria-hidden="true"
    />

    <div class="overflow-hidden rounded-lg bg-zinc-950">
      <div class="flex items-center gap-2 border-b border-white/[0.06] bg-zinc-900/90 px-4 py-2.5">
        <span class="size-3 rounded-full bg-red-500/80" />
        <span class="size-3 rounded-full bg-amber-400/80" />
        <span class="size-3 rounded-full bg-emerald-400/80" />
        <span class="mx-auto text-xs font-medium text-zinc-500">app.example.com / bookings / schedule</span>
      </div>

      <div class="flex min-h-[22rem] bg-zinc-950">
        <aside class="hidden w-14 shrink-0 border-r border-white/10 bg-zinc-900/50 p-3 sm:block">
          <div class="space-y-3">
            <div
              v-for="i in 5"
              :key="i"
              class="mx-auto size-8 rounded-lg"
              :class="i === 2 ? 'bg-amber-500/20 ring-1 ring-amber-500/40' : 'bg-white/5'"
            />
          </div>
        </aside>

        <div class="min-w-0 flex-1 p-4 sm:p-6">
          <div class="mb-4 flex items-center justify-between gap-3">
            <div>
              <p class="text-[11px] font-medium uppercase tracking-wider text-zinc-500">
                Bookings
              </p>
              <h3 class="text-sm font-semibold text-white sm:text-base">
                Select dates
              </h3>
            </div>
            <div class="hidden rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs text-zinc-400 sm:block">
              Multi-range mode
            </div>
          </div>

          <div class="rounded-xl border border-white/10 bg-zinc-900/60 p-3 sm:p-4">
            <div class="mb-3 flex items-center justify-between gap-2">
              <button
                type="button"
                class="rounded-md border border-white/10 px-2 py-1 text-xs text-zinc-400"
              >
                ‹
              </button>
              <span class="text-xs font-semibold text-zinc-200 sm:text-sm">July – September 2026</span>
              <button
                type="button"
                class="rounded-md border border-white/10 px-2 py-1 text-xs text-zinc-400"
              >
                ›
              </button>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
              <div
                v-for="month in months"
                :key="month.label"
                class="rounded-lg border border-white/10 bg-zinc-950/80 p-2.5"
              >
                <p class="mb-2 text-center text-[11px] font-semibold text-zinc-300">
                  {{ month.label }}
                </p>
                <div class="mb-1 grid grid-cols-7 gap-0.5 text-center text-[9px] text-zinc-500">
                  <span
                    v-for="weekday in weekdays"
                    :key="weekday"
                  >{{ weekday }}</span>
                </div>
                <div class="grid grid-cols-7 gap-0.5">
                  <span
                    v-for="(cell, index) in month.days"
                    :key="`${month.label}-${index}`"
                    class="flex aspect-square items-center justify-center rounded-md text-[10px] font-medium"
                    :class="cell.day ? dayClass(cell.state) : ''"
                  >
                    {{ cell.day ?? '' }}
                  </span>
                </div>
              </div>
            </div>

            <div class="mt-3 flex justify-end gap-2">
              <span class="rounded-lg border border-white/10 px-3 py-1.5 text-[11px] text-zinc-400">Cancel</span>
              <span class="rounded-lg bg-amber-500 px-3 py-1.5 text-[11px] font-medium text-white">Save</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
