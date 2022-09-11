<template>
  <div
    class="grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-4 md:grid-cols-4 xl:grid-cols-6 xl:gap-x-4"
    role="group"
  >
    <template v-for="file in files" :key="file.id">
      <File
        :selected="isSelected(file) ?? false"
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
import PreviewModal from '@/components/Modals/PreviewModal'
import File from '@/components/Cards/File'
import { entity } from '@/transformers/entityTransformer'
import { useStore } from '@/store'

const props = defineProps({
  files: {
    type: Array,
    default: [],
  },
})

const store = useStore()

// STATE
const isSelected = computed(() => store.isSelected)
const preview = computed(() => store.preview)

// ACTIONS
const openPreview = file => (store.preview = file)
const toggleSelection = file => store.toggleSelection({ file })
</script>