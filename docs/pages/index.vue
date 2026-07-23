<script setup lang="ts">
const { data: page } = await useAsyncData('content-home', () => queryContent('/').findOne())

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
