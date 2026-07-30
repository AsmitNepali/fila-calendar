<script setup lang="ts">
import { withBase } from 'ufo'

const { app: { baseURL } } = useRuntimeConfig()

const videoSrc = computed(() => withBase('/videos/fila-calendar-demo.mp4', baseURL))

/* A 37s loop is motion nobody asked for. The server renders it autoplaying, so readers who opted
   out are paused on mount and handed controls instead. */
const prefersReducedMotion = ref(false)
const video = useTemplateRef<HTMLVideoElement>('video')

onMounted(() => {
  prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches

  if (prefersReducedMotion.value) {
    video.value?.pause()
  }
})
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

      <video
        ref="video"
        :src="videoSrc"
        class="block aspect-[1920/934] w-full bg-zinc-950"
        :autoplay="!prefersReducedMotion"
        :loop="!prefersReducedMotion"
        :controls="prefersReducedMotion"
        muted
        playsinline
        preload="metadata"
        aria-label="Selecting single days, multiple days, a range, and several ranges in a Filament form"
      />
    </div>
  </div>
</template>
