<template>
  <ul
    class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-4 md:grid-cols-4 xl:grid-cols-6 xl:gap-x-4"
    role="list"
  >
    <li v-for="file in files">
      <component
        :is="fileCardComponent(file)"
        :checked="isFileSelected(file) ?? false"
        :file="file"
        @click="toggleSelection(file)"
        @dblclick="openPreview(file)"
      />

      <preview-modal :file="file"/>
    </li>
  </ul>
</template>

<script setup>
import ImageCard from './Cards/ImageCard.vue'
import VideoCard from './Cards/VideoCard.vue'
import FileCard from './Cards/FileCard.vue'
import { useStore } from 'vuex'
import PreviewModal from '@/components/Modals/PreviewModal'
import { computed, ref } from 'vue'

const store = useStore()
const clicks = ref(0)
const timer = ref(null)

const files = computed(() => store.state['nova-file-manager'].files)
const selection = computed(() => store.state['nova-file-manager'].selection)
const isFileSelected = computed(() => store.getters['nova-file-manager/isFileSelected'])

const fileCardComponent = (file) => {
  switch (file.type) {
    case 'image':
      return ImageCard
    case 'video':
      return VideoCard
    default:
      return FileCard
  }
}

const openPreview = (file) => store.commit('nova-file-manager/previewFile', file)
const toggleSelection = (file) => store.getters['nova-file-manager/isFileSelected'](file)
  ? store.commit('nova-file-manager/deselectFile', file)
  : store.commit('nova-file-manager/selectFile', file)
</script>
