<template>
  <div class="nova-file-manager" :class="{ dark }" id="tour-container"></div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import Shepherd from 'shepherd.js'
import { useTourStore } from '@/store/tour'
import { useStore } from '@/store'

const store = useStore()
const tourStore = useTourStore()

const dark = computed(() => store.dark)

onMounted(() => {
  if (!Shepherd.activeTour) {
    tourStore.init()
  }
})
</script>
