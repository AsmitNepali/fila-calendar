export default defineAppConfig({
  ui: {
    colors: {
      primary: 'amber',
      neutral: 'slate'
    },
    footer: {
      slots: {
        root: 'border-t border-default',
        left: 'text-sm text-muted'
      }
    }
  },
  seo: {
    siteName: 'Fila Calendar'
  },
  header: {
    title: '',
    to: '/',
    logo: {
      alt: 'Fila Calendar',
      light: '/logo.png',
      dark: '/logo.png'
    },
    search: true,
    colorMode: true,
    links: [{
      icon: 'i-simple-icons-github',
      to: 'https://github.com/AsmitNepali/fila-calendar',
      target: '_blank',
      'aria-label': 'GitHub'
    }]
  },
  footer: {
    credits: `Fila Calendar • © ${new Date().getFullYear()}`,
    colorMode: false,
    links: [{
      icon: 'i-simple-icons-github',
      to: 'https://github.com/AsmitNepali/fila-calendar',
      target: '_blank',
      'aria-label': 'GitHub'
    }]
  },
  toc: {
    title: 'On this page',
    bottom: {
      title: 'Links',
      edit: 'https://github.com/AsmitNepali/fila-calendar/edit/main/docs/content',
      links: [{
        icon: 'i-lucide-star',
        label: 'Star on GitHub',
        to: 'https://github.com/AsmitNepali/fila-calendar',
        target: '_blank'
      }]
    }
  }
})
