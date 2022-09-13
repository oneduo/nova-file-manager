<template>
  <div
    class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-4 md:grid-cols-4 xl:grid-cols-6 xl:gap-x-4"
    role="list"
  >
    <DirectoryCard
      v-for="directory in folders"
      :key="directory.id"
      :id="directory.id"
      :disk="directory.disk"
      :name="directory.name"
      :path="directory.path"
    />
  </div>
</template>

<script setup>
import DirectoryCard from '@/components/Cards/DirectoryCard'
import { defineProps } from 'vue'

defineProps({
  folders: {
    type: Array,
    default: () => [],
  },
})
</script>
