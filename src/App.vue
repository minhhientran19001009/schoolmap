<template>
  <div id="app" class="w-screen h-screen overflow-hidden">
    <router-view />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { schoolService } from './services/schoolService'

onMounted(() => {
  // Initialize initial school dataset in localStorage
  schoolService.init()
})
</script>
