<template>
  <UContainer>
    <UPage>
      <template #left>
        <UPageAside>
          <DocsNavigation />
        </UPageAside>
      </template>

      <slot />
    </UPage>
  </UContainer>
</template>
