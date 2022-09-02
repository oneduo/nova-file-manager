<template>
  <TransitionRoot :show="isOpen" as="template" class="nova-file-manager">
    <Dialog as="div" class="relative z-[60]" style="z-index: 999" @close="closeModal">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 backdrop-blur-sm transition-opacity bg-gray-800/20" />
      </TransitionChild>

      <div :class="darkMode && 'dark'" class="fixed z-10 inset-0 overflow-y-auto">
        <div
          class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0 max-w-4xl mx-auto"
        >
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
              class="relative bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8"
            >
              <div class="flex flex-col justify-center gap-6 rounded-md px-6 pt-5 pb-6">
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
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { computed, onBeforeUnmount } from 'vue'
import { useStore } from 'vuex'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import File from '@/components/Cards/File'
import entityTransformer from '@/transformers/entityTransformer'

const store = useStore()
const props = defineProps({
    name: String,
})

const darkMode = computed(() => store.state['nova-file-manager'].darkMode)
const isOpen = computed(() => store.getters['nova-file-manager/allModals'].includes(props.name))

const queue = computed(() => store.state['nova-file-manager'].uploadQueue)

const closeModal = () => store.dispatch('nova-file-manager/closeModal', props.name)

onBeforeUnmount(() => {
    if (isOpen.value) {
        closeModal()
    }
})
</script>
