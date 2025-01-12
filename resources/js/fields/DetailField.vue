<script setup lang="ts">
import { Entity } from '__types__'
import { computed, onMounted, ref } from 'vue'
import FieldCard from '@/components/Cards/FieldCard.vue'
import PreviewModal from '@/components/Modals/PreviewModal.vue'
import { useClipboard } from '@/hooks'
import useBrowserStore from '@/stores/browser'

interface Props {
  field: any
  index: number
}

const props = defineProps<Props>()

const selected = ref(undefined as Entity | undefined)
const store = useBrowserStore()
const { copy: clipboardCopy } = useClipboard()

const dark = computed(() => store.dark)
const preview = computed(() => store.preview)

const copy = (file: Entity) => {
  selected.value = file
  clipboardCopy(file.url)

  setTimeout(() => {
    selected.value = undefined
  }, 1000)
}

onMounted(() => {
  store.syncDarkMode()
  store.prepareTool({
    singleDisk: props.field.singleDisk,
    permissions: props.field.permissions,
    tour: props.field.tour,
    usePintura: props.field.usePintura || false,
    pinturaOptions: props.field.pinturaOptions || {},
    cropperOptions: props.field.cropperOptions || {},
  })
})
</script>

<template>
  <PanelItem :field="field" :index="index">
    <template v-if="field.value" v-slot:value>
      <div class="nova-file-manager">
        <div :class="{ dark }">
          <ul class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-2 w-full" role="group">
            <template v-for="file in field.value" :key="file.id">
              <FieldCard :field="field" :file="file" :attribute="field.attribute" :detail="true" :on-copy="copy" />

              <PreviewModal :file="file" v-if="!!preview && preview.id === file.id" :read-only="true" />
            </template>
          </ul>
        </div>
      </div>
    </template>
  </PanelItem>
</template>
