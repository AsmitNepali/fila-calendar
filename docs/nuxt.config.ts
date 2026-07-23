const baseURL = process.env.NUXT_APP_BASE_URL || '/'

export default defineNuxtConfig({
    modules: ['@nuxt/content'],

    app: {
        baseURL,
        head: {
            title: 'Filament Calendar',
            meta: [
                {
                    name: 'description',
                    content: 'A Flux-style inline calendar field for Filament — no Flux dependency.',
                },
            ],
            link: [
                {
                    rel: 'icon',
                    href: `${baseURL}favicon.svg`,
                },
            ],
        },
    },

    content: {
        highlight: {
            theme: 'github-dark',
        },
    },

    nitro: {
        prerender: {
            crawlLinks: true,
            routes: ['/'],
        },
    },

    compatibilityDate: '2024-11-01',
})
