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

  <UploadQueueModal name="queue" v-if="showUploadFile && queue.length" />

  <Spotlight />

  <Tour v-if="showTour" />
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import Toolbar from '../components/Toolbar.vue'
import Pagination from '../components/Pagination.vue'
import Spinner from '../components/Elements/Spinner.vue'
import BrowserContent from '../components/BrowserContent.vue'
import UploadQueueModal from '../components/Modals/UploadQueueModal.vue'
import { usePermissions } from '../hooks'
import { useStore } from '../store'
import BrowserDragzone from '../components/Elements/BrowserDragzone.vue'
import dataTransferFiles from '../helpers/data-transfer'
import Spotlight from '../components/Modals/Spotlight.vue'
import Tour from '../components/Elements/Tour.vue'

const store = useStore()

// STATE
const files = computed(() => store.files)
const folders = computed(() => store.folders)
const filled = computed(() => !!files.value?.length || !!folders.value?.length)
const pagination = computed(() => store.pagination)
const view = computed(() => store.view)
const isFetchingData = computed(() => store.isFetchingData)
const queue = computed(() => store.queue)
const dragActive = ref(false)
const dragFiles = ref([])
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

const dragDrop = async event => {
  if (!showUploadFile.value) {
    return
  }

  dragFiles.value = await dataTransferFiles(event.dataTransfer.items)
}

const submit = async () => {
  if (!showUploadFile.value) {
    return
  }

  if (!dragFiles.value?.length) {
    return
  }

  store.upload({ files: dragFiles.value })

  store.openModal({ name: 'queue' })

  dragActive.value = false
}

watch(dragFiles, () => submit())

const unsubscribe = store.$onAction(({ name, store, after }) => {
  after(() => {
    if (
      [
        'setDisk',
        'setPath',
        'setPerPage',
        'setPage',
        'setSearch',
        'upload',
        'renameFile',
        'deleteFile',
        'unzipFile',
        'createFolder',
        'renameFolder',
        'deleteFolder',
      ].includes(name)
    ) {
      if (store.errors === null) {
        store.data()
      }
    }
  })
})

onBeforeUnmount(() => {
  unsubscribe()
})
</script>
