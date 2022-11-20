<script setup lang="ts">
import { Entity } from '__types__'
import { computed } from 'vue'
import File from '@/components/Cards/File.vue'
import PreviewModal from '@/components/Modals/PreviewModal.vue'
import useBrowserStore from '@/stores/browser'

interface Props {
  files: Entity[]
}

withDefaults(defineProps<Props>(), {
  files: () => [],
})

const store = useBrowserStore()

// STATE
const isSelected = computed(() => store.isSelected)
const preview = computed(() => store.preview)

// ACTIONS
const openPreview = file => (store.preview = file)
const toggleSelection = file => store.toggleSelection({ file })
</script>

<template>
  <div
    class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-4 md:grid-cols-4 xl:grid-cols-6 xl:gap-x-4"
    role="group"
    data-tour="nfm-file-grid"
  >
    <template v-for="file in files" :key="file.id">
      <File
        :selected="isSelected(file) ?? false"
        :file="file"
        @click="toggleSelection(file)"
        @dblclick="openPreview(file)"
      />

      <PreviewModal :file="file" v-if="!!preview && preview.id === file.id" />
    </template>
  </div>
</template>
