<script setup lang="ts">
import type { ContentNavigationItem } from '@nuxt/content'
import { withoutTrailingSlash } from 'ufo'

const route = useRoute()
const navigation = inject<Ref<ContentNavigationItem[]>>('navigation', () => ref([]))
const items = computed(() => navigation.value)
const navigationKey = computed(() => withoutTrailingSlash(route.path))
</script>

<template>
  <ClientOnly>
    <UContentNavigation
      :key="navigationKey"
      highlight
      :navigation="items"
    />

    <template #fallback>
      <UContentNavigation
        highlight
        :navigation="items"
      />
    </template>
  </ClientOnly>
</template>
