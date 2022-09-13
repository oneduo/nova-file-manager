<template>
  <File
    :file="file"
    :selected="false"
    :on-deselect="onDeselect"
    :single-disk="singleDisk"
    @click.prevent.stop="preview(file)"
  />
</template>

<script setup>
import { computed } from 'vue'
import Entity from '@/types/Entity'
import File from '@/components/Cards/File'
import { useStore } from '@/store'

const store = useStore()

const props = defineProps({
    file: {
        type: Entity,
        required: true,
    },
    detail: {
        type: Boolean,
        default: false,
    },
    field: {
        type: Object,
        required: true,
    },
    onDeselect: {
        type: Function,
    },
})

// STATE
const singleDisk = computed(() => store.singleDisk)

// ACTIONS
const openPreview = file => (store.preview = file)

const preview = file => {
    if (!props.detail) {
        return
    }

    file.exists && openPreview(file)
}
</script>
