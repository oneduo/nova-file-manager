<script setup lang="ts">
import useBrowserStore from '@/stores/browser';
import useTourStore from '@/stores/tour';
import Shepherd from 'shepherd.js';
import { computed, onMounted } from 'vue';

const store = useBrowserStore();
const tourStore = useTourStore();

const dark = computed(() => store.dark);

onMounted(() => {
  if (!Shepherd.activeTour) {
    tourStore.init();
  }
});
</script>

<template>
  <div class="nova-file-manager" :class="{ dark }" id="tour-container"></div>
</template>
