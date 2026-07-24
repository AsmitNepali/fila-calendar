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
