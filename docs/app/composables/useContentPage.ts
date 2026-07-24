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

export async function useDocsNavigation(): Promise<Ref<ContentNavigationItem[]>> {
  const navigation = useState<ContentNavigationItem[]>('docs-navigation', () => [])

  const { data } = await useAsyncData('navigation', () => queryCollectionNavigation('docs'), useContentPageCache('navigation'))

  if (data.value?.length) {
    navigation.value = data.value
  }

  watch(data, (value) => {
    if (value?.length) {
      navigation.value = value
    }
  })

  return navigation
}
