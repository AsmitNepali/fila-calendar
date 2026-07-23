<script setup lang="ts">
const route = useRoute()

const contentPath = computed(() => {
    const slug = route.params.slug

    if (! slug || (Array.isArray(slug) && slug.length === 0)) {
        return '/'
    }

    return `/${Array.isArray(slug) ? slug.join('/') : slug}`
})

const { data: page } = await useAsyncData(`content-${contentPath.value}`, () => {
    return queryContent(contentPath.value).findOne()
})

if (! page.value) {
    throw createError({
        statusCode: 404,
        statusMessage: 'Page not found',
    })
}

useSeoMeta({
    title: page.value?.title ? `${page.value.title} · Filament Calendar` : 'Filament Calendar',
    description: page.value?.description,
})
</script>

<template>
    <article v-if="page" class="prose">
        <ContentRenderer :value="page" />
    </article>
</template>
