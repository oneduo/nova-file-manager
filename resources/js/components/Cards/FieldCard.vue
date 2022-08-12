<template>
  <div
    class="border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 rounded-md p-2 w-full flex flex-col justify-between gap-y-2 h-md"
  >
    <div
      class="flex flex-row p-2 w-full justify-between items-center bg-gray-50/80 dark:bg-gray-600/10 rounded-md"
    >
      <span class="truncate text-xs">{{ file.name }}</span>
    </div>
    <div
      :class="mode !== 'form' && 'cursor-pointer'"
      class="aspect-w-1 aspect-h-1 block w-full overflow-hidden rounded-lg"
      @click="setPreviewFile(file)"
    >
      <div v-if="file.type === 'image'" class="w-full h-full">
        <img :src="file.url" alt="" class="object-cover rounded-md w-full" />
      </div>
      <div v-else-if="file.type === 'video'" class="w-full h-full">
        <video class="w-full">
          <source :src="file.url" />
          Sorry, your browser doesn't support embedded videos.
        </video>
      </div>
      <div
        v-else
        class="m-auto flex items-center justify-center bg-gray-200 dark:bg-gray-900 group-hover:opacity-75 text-gray-500"
      >
        <DocumentIcon class="h-16 w-16 text-gray-600" />
      </div>
    </div>
    <div class="flex flex-row justify-between text-xs">
      <span
        class="inline-flex items-center text-xs p-1 rounded font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-400"
        >{{ file.size }}
      </span>

      <button
        v-if="mode === 'form'"
        class="bg-red-400/20 dark:bg-red-800/30 rounded-md p-1 text-red-500"
        @click.prevent="remove(file)"
      >
        <TrashIcon class="w-3 h-3" />
      </button>

      <Transition v-else mode="out-in" name="fade">
        <button
          v-if="selected"
          class="rounded-md p-1 transition-all bg-green-400/20 dark:bg-green-800/30 text-green-500"
        >
          <CheckIcon class="w-4 h-4" />
        </button>
        <button
          v-else
          class="rounded-md p-1 transition-all bg-blue-400/20 dark:bg-blue-800/30 text-blue-500"
          @click.prevent="copy(file)"
        >
          <ClipboardCopyIcon class="w-4 h-4" />
        </button>
      </Transition>
    </div>

    <PreviewModal
      v-if="showPreviewModal && shouldShowPreviewModal"
      :file="file"
      :without-actions="true"
    />
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useStore } from 'vuex'
import {
    CheckIcon,
    ClipboardCopyIcon,
    DocumentIcon,
    TrashIcon,
} from '@heroicons/vue/outline'
import PreviewModal from '@/components/Modals/PreviewModal'
import { useClipboard } from '@/hooks'

const store = useStore()
const props = defineProps({
    file: {
        type: Object,
        required: true,
    },
    mode: {
        type: String,
        required: true,
        default: () => 'detail',
    },
    field: {
        type: Object,
        required: true,
    },
})

const { copyToClipboard } = useClipboard()
const selected = ref(false)
const showPreviewModal = computed(() => props.mode !== 'form')
const preview = computed(() => store.state['nova-file-manager'].preview)
const shouldShowPreviewModal = ref(false)

watch(preview, () => {
    if (preview.value === null) {
        shouldShowPreviewModal.value = false
    }
})

const copy = file => {
    selected.value = file
    copyToClipboard(file.url)

    setTimeout(() => {
        selected.value = false
    }, 1000)
}

const setPreviewFile = file => {
    if (!showPreviewModal.value) {
        return
    }

    shouldShowPreviewModal.value = true
    store.commit('nova-file-manager/previewFile', file)
}

const remove = file =>
    store.commit('nova-file-manager/deselectFieldFile', {
        field: props.field.attribute,
        file,
    })
</script>
