<script setup>
import { containerMaxW } from '@/config.js'
import BaseLevel from '@/Components/Admin/BaseLevel.vue'

const year = new Date().getFullYear()
</script>

<template>
  <footer class="py-2 px-6" :class="containerMaxW">
    <BaseLevel>
      <div class="text-center md:text-left">
        <b>&copy;{{ year }}, <Link :href="route('dashboard')" target="_blank">E-trail.eu</Link>.</b>
        <slot />
      </div>
      <div class="md:py-2">
        <Link :href="route('dashboard')">
            <img
                class="h-10 ml-4"
                src="../../../logo.png"
                alt="Logo"
            />
        </Link>
      </div>
    </BaseLevel>
  </footer>
</template>
