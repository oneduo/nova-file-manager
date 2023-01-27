<script setup lang="ts">
import { Entity, NovaField } from '__types__'
import { computed } from 'vue'
import File from '@/components/Cards/File.vue'
import useBrowserStore from '@/stores/browser'

const store = useBrowserStore()

interface Props {
  file: Entity
  detail?: boolean
  field: NovaField
  onDeselect?: (file: Entity) => void
}

const props = withDefaults(defineProps<Props>(), {
  detail: false,
})

// STATE
const singleDisk = computed(() => store.singleDisk)

// ACTIONS
const openPreview = (file: Entity) => (store.preview = file)

const preview = (file: Entity) => {
  if (!props.detail) {
    return
  }

  file.exists && openPreview(file)
}
</script>

<template>
  <File
    :file="file"
    :selected="false"
    :on-deselect="onDeselect"
    :single-disk="singleDisk"
    :field-mode="true"
    @click.prevent.stop="preview(file)"
  />
</template>
