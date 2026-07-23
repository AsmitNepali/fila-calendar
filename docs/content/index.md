---
seo:
  title: Fila Calendar
  description: A polished inline calendar field for Filament forms and infolists.
---

::u-page-section
#title
Everything you need for date selection

#features
  :::u-page-feature
  ---
  icon: i-lucide-calendar-range
  ---
  #title
  Four selection modes

  #description
  Single dates, multiple dates, one range, or many ranges — all with consistent state hydration and dehydration.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-mouse-pointer-click
  ---
  #title
  Range hover preview

  #description
  After picking a start date, hover toward the end date to preview in-between days before the second click.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-scissors
  ---
  #title
  Smart range splitting

  #description
  Click a middle day in a multi-range to split it into two ranges instead of clearing the whole span.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-languages
  ---
  #title
  Locale support

  #description
  Localize month headers, weekday labels, and dropdowns with `->locale('ja')` or browser defaults.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-layout-grid
  ---
  #title
  Multi-month grids

  #description
  Render up to twelve months with responsive columns, optional scrolling, and selectable headers.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-eye
  ---
  #title
  Form & infolist

  #description
  `CalendarInput` for editable forms and `CalendarEntry` for read-only infolist display with the same API.
  :::
::

::u-page-section{class="bg-neutral-50 dark:bg-gradient-to-b dark:from-neutral-950 dark:to-neutral-900"}
  :::u-page-c-t-a
  ---
  links:
    - label: Installation
      to: '/getting-started/installation'
      trailingIcon: i-lucide-arrow-right
    - label: View on GitHub
      to: 'https://github.com/AsmitNepali/fila-calendar'
      target: _blank
      variant: subtle
      icon: i-simple-icons-github
  title: Ready to add a calendar to your Filament app?
  description: Install with Composer, register assets, and drop a CalendarInput into your next form.
  class: dark:bg-neutral-950
  ---

  :stars-bg
  :::
::
