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
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import Toolbar from '@/components/Toolbar'
import Pagination from '@/components/Pagination'
import Spinner from '@/components/Elements/Spinner'
import BrowserContent from '@/components/BrowserContent'
import UploadQueueModal from '@/components/Modals/UploadQueueModal'
import { usePermissions } from '@/hooks'
import { useStore } from '@/store'
import BrowserDragzone from '@/components/Elements/BrowserDragzone'
import dataTransferFiles from '@/helpers/data-transfer'

const store = useStore()

// STATE
const files = computed(() => store.files)
const folders = computed(() => store.folders)
const filled = computed(() => !!files.value?.length || !!folders.value?.length)
const pagination = computed(() => store.pagination)
const view = computed(() => store.view)
const isFetchingData = computed(() => store.isFetchingData)
const queue = computed(() => store.queue)

// ACTIONS
const { showUploadFile } = usePermissions()

onMounted(() => {
    store.init()

    if (!store.singleDisk && !store.disks) {
        store.getDisks()
    }

    store.data()
})

const dragActive = ref(false)
const dragFiles = ref([])
const dataTransfer = ref(null)

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

const dragDrop = event => {
    if (!showUploadFile.value) {
        return
    }

    dragFiles.value = event.dataTransfer.files
    dataTransfer.value = event.dataTransfer
}

const submit = async () => {
    if (!showUploadFile.value) {
        return
    }

    if (dataTransfer.value === null) {
        return
    }

    const files = await dataTransferFiles(dataTransfer.value.items)
    console.log(files)

    store.upload({ files })

    store.openModal({ name: 'queue' })

    dragActive.value = false
    dataTransfer.value = null
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
                'createFolder',
                'renameFolder',
                'deleteFolder',
            ].includes(name)
        ) {
            store.data()
        }
    })
})

onBeforeUnmount(() => {
    unsubscribe()
})
</script>
