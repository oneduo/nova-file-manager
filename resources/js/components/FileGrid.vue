<template>
  <div
    class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-4 md:grid-cols-4 xl:grid-cols-6 xl:gap-x-4"
    role="group"
  >
    <template v-for="file in files" :key="file.id">
      <File
        :selected="isFileSelected(file) ?? false"
        :file="entity(file)"
        @click="toggleSelection(file)"
        @dblclick="openPreview(file)"
      />

      <PreviewModal :file="entity(file)" v-if="!!preview && preview?.id === entity(file)?.id" />
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import PreviewModal from '@/components/Modals/PreviewModal'
import File from '@/components/Cards/File'
import { entity } from '@/transformers/entityTransformer'

const store = useStore()

const files = computed(() => store.state['nova-file-manager'].files)
const isFileSelected = computed(() => store.getters['nova-file-manager/isFileSelected'])
const preview = computed(() => store.state['nova-file-manager'].preview)

const openPreview = file => store.commit('nova-file-manager/previewFile', file)
const toggleSelection = file => store.commit('nova-file-manager/toggleSelection', file)
</script>
