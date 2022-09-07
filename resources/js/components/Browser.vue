<template>
  <div class="flex flex-1 items-stretch overflow-hidden rounded-md w-full min-h-[30vh]">
    <main class="relative flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-800">
      <div class="w-full px-4 space-y-4 mb-4">
        <Toolbar />

        <div v-if="isFetchingData" class="w-full h-80 flex justify-center items-center">
          <Spinner class="w-16 h-16" />
        </div>

        <div v-else @dragover.prevent.stop="dragenter" @drop.prevent="onDrop" class="relative">
          <div
            v-if="showUploadFile && isDragging"
            @dragleave.prevent.self="dragleave"
            class="absolute inset-0 z-50 pt-16 bg-gray-100/90 dark:bg-gray-700/80 rounded-md backdrop-blur-sm w-full h-full flex justify-start flex-col items-center border-2 border-blue-500"
          >
            <CloudArrowUpIcon class="w-16 h-16 text-blue-500 animate-bounce" />
            <p class="font-bold text-gray-900 dark:text-gray-50 p-2 rounded-md">
              {{ __('NovaFileManager.dropzone.prompt') }}
            </p>
          </div>
          <BrowserContent :directories="directories" :files="files" :filled="filled" :view="view" />
        </div>
      </div>

      <Pagination
        v-if="!isFetchingData && pagination && pagination.total > 0"
        :current-page="pagination.current_page"
        :from="pagination.from"
        :last-page="pagination.last_page"
        :links="pagination.links"
        :to="pagination.to"
        :total="pagination.total"
        class="mt-auto"
      />
    </main>
  </div>

  <UploadQueueModal name="upload-queue" v-if="showUploadFile && queue.length" />
  <div id="modals"></div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useStore } from 'vuex'
import Toolbar from '@/components/Toolbar'
import Pagination from '@/components/Pagination'
import Spinner from '@/components/Elements/Spinner'
import BrowserContent from '@/components/BrowserContent'
import { CloudArrowUpIcon } from '@heroicons/vue/24/outline'
import UploadQueueModal from '@/components/Modals/UploadQueueModal'
import { usePermissions } from '@/hooks'

const store = useStore()
const files = computed(() => store.state['nova-file-manager'].files)
const directories = computed(() => store.state['nova-file-manager'].directories)
const filled = computed(() => files.value?.length || directories.value?.length)
const pagination = computed(() => store.state['nova-file-manager'].pagination)
const view = computed(() => store.state['nova-file-manager'].view)
const isFetchingData = computed(() => store.state['nova-file-manager'].isFetchingData)
const queue = computed(() => store.state['nova-file-manager'].queue)

const { showUploadFile } = usePermissions()

onMounted(() => {
    store.commit('nova-file-manager/init')

    if (!store.state['nova-file-manager'].customDisk) {
        store.dispatch('nova-file-manager/getDisks')
    }

    store.dispatch('nova-file-manager/getData')
})

const isDragging = ref(false)
const draggedFiles = ref([])

const dragenter = () => {
    if (showUploadFile.value) {
        isDragging.value = true
    }
}

const dragleave = () => {
    if (showUploadFile.value) {
        isDragging.value = false
    }
}
const onDrop = e => {
    if (showUploadFile.value) {
        draggedFiles.value = e.dataTransfer.files
    }
}

const submit = () => {
    if (showUploadFile.value && draggedFiles.value.length) {
        store.dispatch('nova-file-manager/upload', draggedFiles.value)

        store.dispatch('nova-file-manager/openModal', 'upload-queue')

        isDragging.value = false
    }
}

watch(draggedFiles, () => submit())
</script>
