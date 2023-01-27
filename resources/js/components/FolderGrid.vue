<script setup lang="ts">
import type { Folder as FolderType } from '__types__'
import Folder from '@/components/Cards/Folder.vue'

interface Props {
  folders: FolderType[]
}

withDefaults(defineProps<Props>(), {
  folders: () => [],
})
</script>

<template>
  <div
    class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-4 md:grid-cols-4 xl:grid-cols-6 xl:gap-x-4"
    role="list"
    data-tour="nfm-folder-grid"
  >
    <Folder v-for="folder in folders" :key="folder.id" :folder="folder" />
  </div>
</template>
