<script setup lang="ts">
import { useClipboard } from '@vueuse/core'
import { withBase } from 'ufo'

const route = useRoute()
const toast = useToast()
const { copy, copied } = useClipboard()
const { app: { baseURL } } = useRuntimeConfig()
const requestURL = useRequestURL()

const pagePath = computed(() => route.path.replace(/\/$/, '') || '/')
const rawMarkdownPath = computed(() => withBase(`/raw${pagePath.value}.md`, baseURL))
const mdPath = computed(() => new URL(rawMarkdownPath.value, requestURL.origin).href)
const aiPrompt = computed(() => `Read ${mdPath.value} so I can ask questions about it.`)

const items = computed(() => [
  {
    label: 'Copy Markdown link',
    icon: 'i-lucide-link',
    onSelect() {
      copy(mdPath.value)
      toast.add({
        title: 'Copied to clipboard',
        icon: 'i-lucide-check-circle'
      })
    }
  },
  {
    label: 'View as Markdown',
    icon: 'i-simple-icons:markdown',
    onSelect() {
      window.open(rawMarkdownPath.value, '_blank', 'noopener,noreferrer')
    }
  },
  {
    label: 'Open in ChatGPT',
    icon: 'i-simple-icons:openai',
    onSelect() {
      window.open(
        `https://chatgpt.com/?hints=search&q=${encodeURIComponent(aiPrompt.value)}`,
        '_blank',
        'noopener,noreferrer'
      )
    }
  },
  {
    label: 'Open in Claude',
    icon: 'i-simple-icons:anthropic',
    onSelect() {
      window.open(
        `https://claude.ai/new?q=${encodeURIComponent(aiPrompt.value)}`,
        '_blank',
        'noopener,noreferrer'
      )
    }
  }
])

async function copyPage() {
  try {
    copy(await $fetch<string>(rawMarkdownPath.value))
    toast.add({
      title: 'Copied to clipboard',
      icon: 'i-lucide-check-circle'
    })
  } catch {
    toast.add({
      title: 'Failed to copy page',
      color: 'error',
      icon: 'i-lucide-circle-x'
    })
  }
}
</script>

<template>
  <UFieldGroup>
    <UButton
      label="Copy page"
      :icon="copied ? 'i-lucide-copy-check' : 'i-lucide-copy'"
      color="neutral"
      variant="outline"
      :ui="{
        leadingIcon: [copied ? 'text-primary' : 'text-neutral', 'size-3.5']
      }"
      @click="copyPage"
    />
    <UDropdownMenu
      :items="items"
      :content="{
        align: 'end',
        side: 'bottom',
        sideOffset: 8
      }"
      :ui="{
        content: 'w-48'
      }"
    >
      <UButton
        icon="i-lucide-chevron-down"
        size="sm"
        color="neutral"
        variant="outline"
        aria-label="Open copy actions menu"
      />
    </UDropdownMenu>
  </UFieldGroup>
</template>
