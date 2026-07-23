<script setup lang="ts">
import CalendarBanner from '~/components/content/CalendarBanner.vue'
import StarsBg from '~/components/content/StarsBg.vue'

const colorMode = useColorMode()
const starColor = computed(() => colorMode.value === 'dark'
  ? 'rgba(255, 255, 255, 0.75)'
  : 'rgba(120, 80, 20, 0.35)')
</script>

<template>
  <section class="relative overflow-hidden border-b border-neutral-200 dark:border-white/5">
    <!-- Base gradient -->
    <div
      class="pointer-events-none absolute inset-0 z-0 bg-gradient-to-b from-white via-amber-50/30 to-neutral-100 dark:from-[#0a0f1a] dark:via-[#070b14] dark:to-black"
      aria-hidden="true"
    />

    <!-- Starfield -->
    <StarsBg
      class="!inset-0 !z-[1] h-full min-h-full opacity-80 dark:opacity-100"
      cover="full"
      :star-count="450"
      :color="starColor"
      speed="slow"
      :size="{ min: 0.5, max: 1.5 }"
    />

    <!-- Center spotlight glow -->
    <div
      class="pointer-events-none absolute inset-0 z-[2] bg-[radial-gradient(ellipse_75%_55%_at_50%_32%,rgba(245,158,11,0.18),transparent_68%)] dark:bg-[radial-gradient(ellipse_75%_55%_at_50%_32%,rgba(255,255,255,0.06),transparent_68%)]"
      aria-hidden="true"
    />

    <!-- Edge vignette -->
    <div
      class="pointer-events-none absolute inset-0 z-[2] bg-[radial-gradient(ellipse_100%_90%_at_50%_50%,transparent_35%,rgba(0,0,0,0.04)_100%)] dark:bg-[radial-gradient(ellipse_100%_90%_at_50%_50%,transparent_30%,rgba(0,0,0,0.55)_100%)]"
      aria-hidden="true"
    />

    <!-- Fine grain noise -->
    <div
      class="pointer-events-none absolute inset-0 z-[2] opacity-[0.18] mix-blend-overlay [background-image:url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJhIiB4PSIwIiB5PSIwIj48ZmVUdXJidWxlbmNlIGJhc2VGcmVxdWVuY3k9Ii43NSIgc3RpdGNoVGlsZXM9InN0aXRjaCIgdHlwZT0iZnJhY3RhbE5vaXNlIi8+PGZlQ29sb3JNYXRyaXggdHlwZT0ic2F0dXJhdGUiIHZhbHVlcz0iMCIvPjwvZmlsdGVyPjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIiBmaWx0ZXI9InVybCgjYSkiIG9wYWNpdHk9Ii4wNSIvPjwvc3ZnPg==')] dark:opacity-[0.35]"
      aria-hidden="true"
    />

    <UContainer class="relative z-10 py-16 sm:py-20 lg:py-24">
      <div class="mx-auto max-w-3xl text-center">
        <p class="mb-4 text-sm font-medium tracking-wide text-amber-600 dark:text-amber-400/90">
          asmit/fila-calendar
        </p>

        <h1 class="text-4xl font-bold tracking-tight text-neutral-900 sm:text-5xl lg:text-6xl dark:text-white">
          Effortless date selection,
          <span class="bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700 bg-clip-text text-transparent dark:from-amber-200 dark:via-amber-400 dark:to-amber-600">built for Filament</span>
        </h1>

        <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-neutral-600 sm:text-lg dark:text-zinc-400">
          A polished inline calendar for forms and infolists. Multi-range selection, hover preview,
          locale support, and no extra UI dependencies.
        </p>

        <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
          <UButton
            to="/getting-started/introduction"
            size="xl"
            trailing-icon="i-lucide-arrow-right"
            class="rounded-full px-6"
          >
            Get started
          </UButton>
          <UButton
            to="https://github.com/AsmitNepali/fila-calendar"
            target="_blank"
            size="xl"
            color="neutral"
            variant="outline"
            icon="i-simple-icons-github"
            class="rounded-full px-6 dark:border-white/15 dark:bg-white/5 dark:text-zinc-200 dark:hover:bg-white/10"
          >
            View on GitHub
          </UButton>
        </div>
      </div>

      <div class="relative mt-14 sm:mt-16 lg:mt-20">
        <CalendarBanner />
      </div>
    </UContainer>
  </section>
</template>
