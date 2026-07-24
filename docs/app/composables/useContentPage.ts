import type { ContentNavigationItem } from '@nuxt/content'
import { withoutTrailingSlash } from 'ufo'

export function useContentPageCache<T>(key: string): {
  getCachedData: (cacheKey: string, nuxtApp: { payload: { data: Record<string, T> }; static: { data: Record<string, T> } }) => T | undefined
} {
  return {
    getCachedData(cacheKey, nuxtApp) {
      return nuxtApp.payload.data[cacheKey] ?? nuxtApp.static.data[cacheKey]
    },
  }
}

export function useContentPath(): string {
  return withoutTrailingSlash(useRoute().path)
}

export async function useDocsNavigation(): Promise<ComputedRef<ContentNavigationItem[]>> {
  const { data: navigation } = await useAsyncData('navigation', () => queryCollectionNavigation('docs'), useContentPageCache('navigation'))

  return computed(() => navigation.value ?? [])
}
