<script setup lang="ts">
import type { ContentNavigationItem } from '@nuxt/content'
import { withBase } from 'ufo'

const route = useRoute()
const navigation = inject<ComputedRef<ContentNavigationItem[]>>('navigation')

const { header, seo } = useAppConfig()
const { app: { baseURL } } = useRuntimeConfig()

const logoSrc = computed(() => withBase(header?.logo?.light ?? header?.logo?.dark ?? '/logo.png', baseURL))
</script>

<template>
  <UHeader
    :ui="{ center: 'flex-1' }"
    :to="header?.to || '/'"
  >
    <UContentSearchButton
      v-if="header?.search"
      :collapsed="false"
      class="w-full"
    />

    <template
      v-if="header?.logo?.dark || header?.logo?.light || header?.title"
      #title
    >
      <div class="flex items-center gap-2.5">
        <img
          v-if="header?.logo?.dark || header?.logo?.light"
          :src="logoSrc"
          :alt="header?.logo?.alt"
          class="size-8 shrink-0 object-contain"
        >

        <span
          v-if="header?.title || seo?.siteName"
          class="font-semibold"
        >
          {{ header?.title || seo?.siteName }}
        </span>
      </div>
    </template>

    <template
      v-else
      #left
    >
      <NuxtLink :to="header?.to || '/'">
        <AppLogo class="w-auto h-6 shrink-0" />
      </NuxtLink>

      <TemplateMenu />
    </template>

    <template #right>
      <UContentSearchButton
        v-if="header?.search"
        class="lg:hidden"
      />

      <UColorModeButton v-if="header?.colorMode" />

      <template v-if="header?.links">
        <UButton
          v-for="(link, index) of header.links"
          :key="index"
          v-bind="{ color: 'neutral', variant: 'ghost', ...link }"
        />
      </template>
    </template>

    <template #body>
      <UContentNavigation
        :key="route.path"
        highlight
        :navigation="navigation"
      />
    </template>
  </UHeader>
</template>
