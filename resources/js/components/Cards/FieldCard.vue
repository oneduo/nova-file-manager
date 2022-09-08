<template>
  <File
    :file="file"
    :selected="false"
    @click.prevent.stop="preview(file)"
    :on-deselect="onDeselect"
    :has-custom-disk="hasCustomDisk"
  />
</template>

<script setup>
import { useStore } from 'vuex'
import File from '@/components/Cards/File'
import { computed } from 'vue'
import Entity from '@/types/Entity'

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

const hasCustomDisk = computed(() => store.state['nova-file-manager'].customDisk)

const openPreview = file => store.commit('nova-file-manager/previewFile', file)

const preview = file => {
    if (!props.detail) {
        return
    }

    props.detail && file.exists && openPreview(file)
}
</script>
