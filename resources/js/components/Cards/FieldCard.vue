<template>
  <File
    :file="mapEntity(file)"
    :selected="false"
    @click="detail && openPreview(file)"
    :on-deselect="onDeselect"
  />
</template>

<script setup>
import { useStore } from 'vuex'
import File from '@/components/Cards/File'
import Entity from '@/types/Entity'

const store = useStore()

defineProps({
    file: {
        type: Object,
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

const openPreview = file => store.commit('nova-file-manager/previewFile', file)

const mapEntity = file =>
    new Entity(
        file.id,
        file.name,
        file.path,
        file.size,
        file.extension,
        file.mime,
        file.url,
        file.lastModifiedAt,
        file.type
    )
</script>
