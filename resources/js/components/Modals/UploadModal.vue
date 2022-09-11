<template>
  <BaseModal name="upload" v-slot="{ close, dark }">
    <DialogPanel
      class="relative bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 max-w-4xl mx-auto"
    >
      <div class="flex flex-col justify-center gap-6 rounded-md px-6 pt-5 pb-6">
        <div v-if="!queue.length" class="space-y-1 text-center p-12">
          <CloudArrowUpIcon
            :class="['mx-auto h-12 w-12 text-blue-500', active && 'animate-bounce']"
          />
          <div class="flex text-sm text-gray-600">
            <label
              class="relative cursor-pointer rounded-md font-medium text-blue-500 hover:underline focus-within:outline-"
              for="file-upload"
            >
              <span>{{ __('NovaFileManager.upload') }}</span>
              <input
                id="file-upload"
                class="sr-only"
                name="file-upload"
                type="file"
                multiple
                @change="onChange"
              />
            </label>
            <p class="pl-1 text-gray-500">
              {{ __('NovaFileManager.drag') }}
            </p>
          </div>
        </div>
        <template v-else>
          <div class="w-full flex flex-row justify-between items-center">
            <h1 class="text-xs uppercase text-gray-400 font-bold">Queue</h1>
          </div>
          <ul class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <template v-for="item in queue" :key="item.id">
              <File
                :file="entityTransformer(item.file)"
                :is-uploading="true"
                :is-uploaded="item.status"
                :upload-ratio="item.ratio"
                :selected="false"
                class="cursor-default"
              />
            </template>
          </ul>
        </template>
      </div>
    </DialogPanel>
  </BaseModal>
</template>

<script setup>
import {ref, watch} from 'vue'
import {DialogPanel} from '@headlessui/vue'
import {CloudArrowUpIcon} from '@heroicons/vue/24/outline'
import File from '@/components/Cards/File'
import entityTransformer from '@/transformers/entityTransformer'
import BaseModal from '@/components/Modals/BaseModal'

const props = defineProps({
  name: {
    type: String,
    default: 'upload',
  },
  queue: {
    type: Array,
  },
  upload: {
    type: Function,
  },
})

// STATE
const active = ref(false)
const files = ref([])

// ACTIONS
const dragEnter = () => (active.value = true)
const dragLeave = () => (active.value = false)
const dragDrop = e => (files.value = e.dataTransfer.files)
const onChange = e => (files.value = e.target.files)

const submit = () => {
  if (files.value.length) {
    props.upload(files.value)
  }

  active.value = false
}

// HOOKS
watch(files, () => submit())
</script>