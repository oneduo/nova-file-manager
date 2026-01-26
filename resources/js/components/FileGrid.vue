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
const allowedExtensions = computed(() => store.allowedExtensions)
const showAdditionalInfo = computed(() => store.showAdditionalInfo)

// HELPERS
const isFileAllowed = (file: Entity): boolean => {
  if (!allowedExtensions.value || allowedExtensions.value.length === 0) {
    return true
  }
  
  const fileExtension = file.name.split('.').pop()?.toLowerCase()
  if (!fileExtension) {
    return false
  }
  
  return allowedExtensions.value.includes(fileExtension)
}

// ACTIONS
const openPreview = (file: Entity) => (store.preview = file)
const toggleSelection = (file: Entity) => {
  if (!isFileAllowed(file)) {
    return
  }
  store.toggleSelection({ file })
}
</script>

<template>
  <div
    class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-4 md:grid-cols-4 xl:grid-cols-6 xl:gap-x-4"
    role="group"
    data-tour="nfm-file-grid"
  >
    <File
      v-for="file in files"
      :key="file.id"
      :selected="isSelected(file) ?? false"
      :file="file"
      :class="[
        !isFileAllowed(file) ? 'opacity-30 cursor-not-allowed pointer-events-none' : ''
      ]"
      :title="!isFileAllowed(file) ? `Allowed extensions: ${allowedExtensions?.join(', ')}` : ''"
      :show-additional-info="showAdditionalInfo"
      @click="toggleSelection(file)"
      @dblclick="openPreview(file)"
    />

    <PreviewModal :file="preview" v-if="!!preview" />
  </div>
</template>
