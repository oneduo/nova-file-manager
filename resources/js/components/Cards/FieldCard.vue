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
  resourceName?: string
  resourceId?: string | number
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

  store.configure({
    initialFiles: props.field.value ?? [],
    multiple: props.field.multiple ?? false,
    limit: props.field.limit ?? null,
    wrapper: props.field.wrapper ?? null,
    resource: props.resourceName ?? undefined,
    resourceId: props.resourceId,
    attribute: props.field.attribute,
    singleDisk: props.field.singleDisk ?? false,
    permissions: props.field.permissions,
    flexibleGroup: [],
    callback: undefined,
    usePintura: false,
    pinturaOptions: undefined,
    cropperOptions: undefined,
    perPage: 10,
    paginationOptions: undefined,
    component: undefined,
  })
  store.setDisk({
    disk: props.field.singleDisk ? 'default' : props.field.value[0].disk,
  })

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
