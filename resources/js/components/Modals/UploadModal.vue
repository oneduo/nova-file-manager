<script setup lang="ts">
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { CloudArrowUpIcon } from '@heroicons/vue/24/outline'
import { QueueEntry } from '__types__'
import { computed, ref, watch } from 'vue'
import { QUEUE_MODAL_NAME } from '@/constants'
import dataTransferFiles from '@/helpers/data-transfer'
import useBrowserStore from '@/stores/browser'

interface Props {
  name: string
  queue: QueueEntry[]
  upload: (files: File[]) => void
}

const props = defineProps<Props>()

// STATE
const active = ref(false)
const files = ref<File[] | FileList>()

const store = useBrowserStore()
const isOpen = computed(() => store.isOpen(props.name))
const darkMode = computed(() => store.dark)

// ACTIONS
const dragEnter = () => (active.value = true)
const dragLeave = () => (active.value = false)
const dragDrop = async (e: DragEvent) => (files.value = await dataTransferFiles(e.dataTransfer?.items))
const onChange = (e: Event) => (files.value = (e.target as HTMLInputElement).files ?? [])
const closeModal = () => store.closeModal({ name: props.name })
const openModal = (name: string) => store.openModal({ name })

const submit = () => {
  if (files.value?.length) {
    if (files.value instanceof FileList) {
      props.upload(Array.from(files.value))
    }

    if (files.value instanceof Array) {
      props.upload(files.value)
    }
  }

  closeModal()

  openModal(QUEUE_MODAL_NAME)

  active.value = false
}

// HOOKS
watch(files, () => submit())
</script>

<template>
  <TransitionRoot :show="isOpen" as="template" class="nova-file-manager">
    <Dialog
      as="div"
      class="relative z-[60]"
      style="z-index: 999"
      @close="closeModal"
      @dragover.prevent.stop="dragEnter"
      @dragleave.prevent.stop="dragLeave"
      @drop.prevent="dragDrop"
    >
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div
          :class="['fixed inset-0  backdrop-blur-sm transition-opacity', active ? 'bg-blue-900/20' : 'bg-gray-800/20']"
        />
      </TransitionChild>

      <div :class="darkMode && 'dark'" class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel
              class="relative bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 max-w-4xl mx-auto"
            >
              <div class="flex flex-col justify-center gap-6 rounded-md px-6 pt-5 pb-6">
                <div v-if="!queue.length" class="space-y-1 text-center p-12">
                  <CloudArrowUpIcon :class="['mx-auto h-12 w-12 text-blue-500', active && 'animate-bounce']" />
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
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
