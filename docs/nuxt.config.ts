// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: [
    '@nuxt/eslint',
    '@nuxt/image',
    '@nuxt/ui',
    '@nuxt/content',
    'nuxt-og-image',
    'nuxt-llms'
  ],

  devtools: {
    enabled: true
  },

  app: {
    baseURL: process.env.NUXT_APP_BASE_URL || '/'
  },

  site: {
    url: 'https://asmitnepali.github.io',
    name: 'Fila Calendar'
  },

  css: ['~/assets/css/main.css'],

  icon: {
    mode: 'css',
    clientBundle: {
      scan: true,
      sizeLimitKb: 512,
      icons: [
        'lucide:house',
        'lucide:code-2',
        'lucide:download',
        'lucide:code',
        'lucide:calendar-range',
        'lucide:calendar-days',
        'lucide:mouse-pointer-click',
        'lucide:scissors',
        'lucide:languages',
        'lucide:layout-grid',
        'lucide:eye',
        'lucide:settings',
        'lucide:link',
        'lucide:check-circle',
        'lucide:copy',
        'lucide:copy-check',
        'lucide:chevron-down',
        'lucide:circle-x',
        'lucide:star',
        'lucide:external-link',
        'lucide:menu',
        'lucide:search',
        'lucide:moon',
        'lucide:sun',
        'simple-icons:github',
        'simple-icons:markdown',
        'simple-icons:openai',
        'simple-icons:anthropic'
      ]
    },
    serverBundle: {
      collections: ['lucide', 'simple-icons']
    }
  },

  content: {
    build: {
      markdown: {
        highlight: {
          langs: ['php']
        },
        toc: {
          searchDepth: 1
        }
      }
    },
    experimental: {
      sqliteConnector: 'native'
    }
  },

  experimental: {
    asyncContext: true
  },

  compatibilityDate: '2026-06-30',

  nitro: {
    prerender: {
      routes: [
        '/'
      ],
      crawlLinks: true
    }
  },

  eslint: {
    config: {
      stylistic: {
        commaDangle: 'never',
        braceStyle: '1tbs'
      }
    }
  },

  llms: {
    domain: 'https://asmitnepali.github.io/fila-calendar/',
    title: 'Fila Calendar',
    description: 'Documentation for asmit/fila-calendar — a polished inline calendar field for Filament.',
    full: {
      title: 'Fila Calendar - Full Documentation',
      description: 'Complete documentation for the Fila Calendar package.'
    },
    sections: [
      {
        title: 'Getting Started',
        contentCollection: 'docs',
        contentFilters: [
          { field: 'path', operator: 'LIKE', value: '/getting-started%' }
        ]
      },
      {
        title: 'Guide',
        contentCollection: 'docs',
        contentFilters: [
          { field: 'path', operator: 'LIKE', value: '/guide%' }
        ]
      }
    ]
  },

  ogImage: {
    zeroRuntime: true
  }
})
