<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import BrowserContent from '@/components/BrowserContent.vue'
import BrowserDragzone from '@/components/Elements/BrowserDragzone.vue'
import Spinner from '@/components/Elements/Spinner.vue'
import Tour from '@/components/Elements/Tour.vue'
import Spotlight from '@/components/Modals/Spotlight.vue'
import UploadQueueModal from '@/components/Modals/UploadQueueModal.vue'
import Pagination from '@/components/Pagination.vue'
import Toolbar from '@/components/Toolbar.vue'
import { QUEUE_MODAL_NAME, WATCHABLE_ACTIONS } from '@/constants'
import dataTransfer from '@/helpers/data-transfer'
import { usePermissions } from '@/hooks'
import useBrowserStore from '@/stores/browser'

const store = useBrowserStore()

// STATE
const files = computed(() => store.files)
const folders = computed(() => store.folders)
const filled = computed(() => !!files.value?.length || !!folders.value?.length)
const pagination = computed(() => store.pagination)
const view = computed(() => store.view)
const isFetchingData = computed(() => store.isFetchingData)
const queue = computed(() => store.queue)
const dragActive = ref(false)
const dragFiles = ref([] as File[])
const showTour = ref(false)

// ACTIONS
const { showUploadFile } = usePermissions()

// HOOKS
onMounted(() => {
  store.init()

  if (!store.singleDisk && !store.disks) {
    store.getDisks()
  }

  store.data()

  setTimeout(() => {
    showTour.value = store.tour
  }, 1000)
})

const dragEnter = () => {
  if (!showUploadFile.value) {
    return
  }

  dragActive.value = true
}

const dragLeave = () => {
  if (!showUploadFile.value) {
    return
  }

  dragActive.value = false
}

const dragDrop = async (event: DragEvent) => {
  if (!showUploadFile.value) {
    return
  }

  dragFiles.value = (await dataTransfer(event.dataTransfer?.items)) as File[]
}

const submit = async () => {
  if (!showUploadFile.value) {
    return
  }

  if (!dragFiles.value?.length) {
    return
  }

  store.upload({ files: dragFiles.value })

  store.openModal({ name: QUEUE_MODAL_NAME })

  dragActive.value = false
}

watch(dragFiles, () => submit())

const unsubscribe = store.$onAction(({ name, store, after }) => {
  after(() => {
    if (WATCHABLE_ACTIONS.includes(name)) {
      if (!store.errors) {
        store.data()
      }
    }
  })
})

onBeforeUnmount(() => {
  unsubscribe()
})
</script>

<template>
  <div class="flex flex-1 items-stretch overflow-hidden rounded-md w-full min-h-[50vh]">
    <main class="relative flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-800">
      <div class="w-full px-4 space-y-4 mb-4">
        <Toolbar />

        <div v-if="isFetchingData" class="w-full h-80 flex justify-center items-center">
          <Spinner class="w-16 h-16" />
        </div>

        <div v-else @dragover.prevent.stop="dragEnter" @drop.prevent="dragDrop" class="relative">
          <BrowserDragzone :drag-leave="dragLeave" v-if="showUploadFile && dragActive" />

          <BrowserContent :folders="folders" :files="files" :filled="filled" :view="view" />
        </div>
      </div>

      <Pagination v-if="!isFetchingData && pagination && pagination.total > 0" class="mt-auto" />
    </main>
  </div>

  <UploadQueueModal :name="QUEUE_MODAL_NAME" v-if="showUploadFile && queue.length" />

  <Spotlight />

  <Tour v-if="showTour" />
</template>
