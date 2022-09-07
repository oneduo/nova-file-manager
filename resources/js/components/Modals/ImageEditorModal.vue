<template>
  <TransitionRoot :show="isOpen" as="template" class="nova-file-manager">
    <Dialog :initial-focus="buttonRef" as="div" class="relative z-[60]" @close="closeModal">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-gray-800/20 backdrop-blur-sm transition-opacity" />
      </TransitionChild>

      <div :class="['fixed z-10 inset-0 overflow-y-auto', darkMode ? 'dark' : '']">
        <div class="flex items-center justify-center min-h-full p-4">
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
              class="relative bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden shadow-xl transform transition-all w-full max-w-7xl p-4 flex flex-col gap-4 min-h-[80vh]"
            >
              <div
                class="w-full flex flex-col flex-col-reverse gap-y-2 md:flex-row justify-between"
              >
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-400 break-all w-full">
                  {{ file.name }}
                </h2>

                <div class="flex flex-row gap-2 justify-end">
                  <IconButton
                    ref="buttonRef"
                    @click.prevent.stop="closeModal(name)"
                    :title="__('NovaFileManager.actions.close')"
                  >
                    <XMarkIcon class="w-5 h-5" />
                  </IconButton>
                </div>
              </div>

              <div class="h-full bg-red-300">
<!--                <div ref="editorRef" class="w-full" style="font-family: 'Nunito', sans-serif!important;"></div>-->
                  <div ref="editorRef" id="editor" class="min-h-[80vh]"></div>
<!--                <ImageEditor :include-ui="true" :options="options"></ImageEditor>-->
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useStore } from 'vuex'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import {
    XMarkIcon,
} from '@heroicons/vue/24/outline'
import IconButton from '@/components/Elements/IconButton'
import DeleteFileModal from '@/components/Modals/DeleteFileModal'
import RenameFileModal from '@/components/Modals/RenameFileModal'
import Entity from '@/types/Entity'
import { useClipboard, usePermissions } from '@/hooks'
import ImageEditor from '@/components/Elements/ImageEditor'

const props = defineProps({
    file: {
        type: Entity,
        required: true,
    },
  name: {
      type: String,
    required: true,
  },
    readOnly: {
        type: Boolean,
        default: false,
    },
})

const store = useStore()
const { copyToClipboard } = useClipboard()
const buttonRef = ref(null)
const editorRef = ref(null)

const darkMode = computed(() => store.state['nova-file-manager'].darkMode)
const isOpen = computed(() => store.getters['nova-file-manager/allModals'].includes(props.name))
const closeModal = name => store.dispatch('nova-file-manager/closeModal', name)
const options = computed(() => ({
  includeUI: {
    loadImage: {
      path: props.file.url,
      name: props.file.name,
    },
    menuBarPosition: 'left'
  },
  cssMaxWidth: 700,
  cssMaxHeight: 500,
  theme: blackTheme
}))

const { showRenameFile, showDeleteFile } = usePermissions()

import { appendDefaultEditor } from "../../pintura.js";

watch(editorRef, (value) => {
  if (value) {
    console.log(value)
    const pintura = appendDefaultEditor("#editor", {
      // The source image to load
      src: props.file.url,

      // This will set a square crop aspect ratio
      imageCropAspectRatio: 1,

      // enableButtonExport: fale,
      enableButtonExport: false,
      locale: { labelButtonExport: 'Save' },

      // Stickers available to user
      stickers: [
        ["Emoji", ["⭐️", "😊", "👍", "👎", "☀️", "🌤", "🌥"]],
      ]
    });

    pintura.on('process', (imageWriterResult) => {
      console.log(imageWriterResult);
      // logs: { src:…, dest:… , imageState:…, store:… }
    });
  }
})
</script>
