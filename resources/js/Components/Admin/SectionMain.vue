<script setup>
import { containerMaxW } from '@/config.js'
</script>

<template>
  <section class="p-6 xl:max-w-7xl xl:mx-auto" :class="containerMaxW">
    <slot />
  </section>
</template>
