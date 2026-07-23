<script setup lang="ts">
const { data: navigation } = await useAsyncData('navigation', () => fetchContentNavigation())
</script>

<template>
    <div class="docs">
        <aside class="docs__sidebar">
            <NuxtLink to="/" class="docs__brand">
                <span class="docs__brand-mark">FC</span>
                <span>Filament Calendar</span>
            </NuxtLink>

            <nav v-if="navigation?.length" class="docs__nav">
                <NuxtLink
                    v-for="link of navigation"
                    :key="link._path"
                    :to="link._path"
                    class="docs__nav-link"
                >
                    {{ link.title }}
                </NuxtLink>
            </nav>
        </aside>

        <main class="docs__main">
            <slot />
        </main>
    </div>
</template>

<style>
:root {
    color-scheme: light dark;
    --bg: #f8fafc;
    --panel: #ffffff;
    --text: #0f172a;
    --muted: #64748b;
    --border: #e2e8f0;
    --accent: #136683;
    --accent-soft: #e0f2f7;
    --code-bg: #0f172a;
}

@media (prefers-color-scheme: dark) {
    :root {
        --bg: #0b1220;
        --panel: #111827;
        --text: #f8fafc;
        --muted: #94a3b8;
        --border: #1f2937;
        --accent: #5ec2e6;
        --accent-soft: #12313d;
        --code-bg: #020617;
    }
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: var(--bg);
    color: var(--text);
}

a {
    color: var(--accent);
}

.docs {
    display: grid;
    grid-template-columns: 280px minmax(0, 1fr);
    min-height: 100vh;
}

.docs__sidebar {
    position: sticky;
    top: 0;
    height: 100vh;
    padding: 1.5rem;
    border-right: 1px solid var(--border);
    background: var(--panel);
}

.docs__brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2rem;
    color: var(--text);
    font-weight: 700;
    text-decoration: none;
}

.docs__brand-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.75rem;
    background: var(--accent-soft);
    color: var(--accent);
    font-size: 0.875rem;
}

.docs__nav {
    display: grid;
    gap: 0.35rem;
}

.docs__nav-link {
    display: block;
    padding: 0.55rem 0.75rem;
    border-radius: 0.6rem;
    color: var(--muted);
    text-decoration: none;
}

.docs__nav-link.router-link-active {
    background: var(--accent-soft);
    color: var(--accent);
    font-weight: 600;
}

.docs__main {
    padding: 2.5rem clamp(1.25rem, 4vw, 4rem) 4rem;
}

.prose {
    max-width: 48rem;
    line-height: 1.7;
}

.prose h1,
.prose h2,
.prose h3 {
    line-height: 1.2;
    scroll-margin-top: 1rem;
}

.prose h1 {
    font-size: clamp(2rem, 4vw, 2.75rem);
    margin: 0 0 1rem;
}

.prose h2 {
    margin: 2.5rem 0 0.75rem;
    font-size: 1.5rem;
}

.prose h3 {
    margin: 1.75rem 0 0.5rem;
    font-size: 1.125rem;
}

.prose p,
.prose ul,
.prose ol,
.prose pre {
    margin: 0 0 1rem;
}

.prose ul,
.prose ol {
    padding-left: 1.25rem;
}

.prose code {
    padding: 0.15rem 0.35rem;
    border-radius: 0.35rem;
    background: var(--accent-soft);
    font-size: 0.92em;
}

.prose pre {
    overflow-x: auto;
    padding: 1rem 1.1rem;
    border-radius: 0.9rem;
    background: var(--code-bg);
    color: #e2e8f0;
}

.prose pre code {
    padding: 0;
    background: transparent;
    color: inherit;
}

.prose table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0 1.5rem;
    font-size: 0.95rem;
}

.prose th,
.prose td {
    padding: 0.65rem 0.75rem;
    border: 1px solid var(--border);
    text-align: left;
}

.prose th {
    background: var(--panel);
}

@media (max-width: 900px) {
    .docs {
        grid-template-columns: 1fr;
    }

    .docs__sidebar {
        position: static;
        height: auto;
        border-right: 0;
        border-bottom: 1px solid var(--border);
    }
}
</style>
