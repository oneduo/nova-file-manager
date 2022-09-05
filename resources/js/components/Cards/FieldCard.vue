<template>
  <File
    :file="entity"
    :selected="false"
    @click.prevent.stop="preview(entity)"
    :on-deselect="onDeselect"
  />
</template>

<script setup>
import { useStore } from 'vuex'
import File from '@/components/Cards/File'
import { computed } from 'vue'
import { entity as mapEntity } from '@/transformers/entityTransformer'

const store = useStore()

const props = defineProps({
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

const entity = computed(() => mapEntity(props.file))

const preview = file => {
    if (!props.detail) {
        return
    }

    props.detail && file.exists && openPreview(file)
}
</script>
