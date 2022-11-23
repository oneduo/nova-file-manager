<script setup lang="ts">
import { DialogPanel } from '@headlessui/vue'
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { ExclamationCircleIcon } from '@heroicons/vue/24/solid'
import { Entity } from '__types__'
import 'cropperjs/dist/cropper.css'
import { computed, ref, watchEffect } from 'vue'
import IconButton from '@/components/Elements/IconButton.vue'
import BaseModal from '@/components/Modals/BaseModal.vue'
import UploadCropModal from '@/components/Modals/UploadCropModal.vue'
import { UPLOAD_CROP_MODAL_NAME } from '@/constants'
import { usePintura } from '@/hooks'
import useBrowserStore from '@/stores/browser'

interface Props {
  file: Entity
  name: string
  onConfirm: (file: File) => void
}

const props = defineProps<Props>()

const store = useBrowserStore()

//STATE
const buttonRef = ref(null)
const loadingError = ref(null as boolean | null)
const destFile = ref(null)
const uploadIsOpen = computed(() => store.isOpen('upload-crop'))
const editorRef = ref(null)
const editor = ref(null)
const { pinturaOptions } = usePintura()

watchEffect(() => {
  if (editorRef.value && !editor.value) {
    try {
      const { appendDefaultEditor, editorOptions } = window.novaFileManagerEditor

      editor.value = appendDefaultEditor(editorRef.value, {
        ...editorOptions,
        ...pinturaOptions.value,
        src: props.file.url,
        enableButtonExport: false,
      })

      if (editor.value) {
        // @ts-ignore Pintura is not typescript friendly
        editor.value.on('loaderror', ({ error }) => window.Nova.error(error.message))
      }
    } catch (e) {
      loadingError.value = true

      console.error(e)
    }
  }
})

// ACTIONS
const openModal = (name: string) => store.openModal({ name })
const closeModal = (name: string) => store.closeModal({ name })

const openUploadCropModal = () => {
  // @ts-ignore Pintura is not typescript friendly
  editor.value?.processImage().then(({ dest }) => {
    destFile.value = dest

    openModal(UPLOAD_CROP_MODAL_NAME)
  })
}

const submitCrop = (name: string) => {
  if (!destFile.value) {
    return
  }

  const file = new File([destFile.value], name, {
    type: props.file.mime,
  })

  closeModal(UPLOAD_CROP_MODAL_NAME)
  closeModal(props.name)

  props.onConfirm(file)
}
</script>

<template>
  <BaseModal as="template" class="nova-file-manager" :name="name" v-slot="{ close }">
    <DialogPanel
      class="relative bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden shadow-xl transform transition-all w-full max-w-7xl p-4 flex flex-col gap-4 h-[80vh] max-h-[80vh]"
    >
      <div class="w-full flex flex-col flex-col-reverse gap-2 md:flex-row justify-between items-start">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-400 break-all w-full">
          {{ __('NovaFileManager.actions.editImage', { image: file.name }) }}
        </h2>

        <div class="flex flex-row gap-2 justify-end flex-shrink-0">
          <IconButton ref="buttonRef" :title="__('NovaFileManager.actions.close')" @click.prevent.stop="close">
            <XMarkIcon class="w-5 h-5" />
          </IconButton>

          <IconButton variant="success" @click="openUploadCropModal">
            <CheckIcon class="h-5 w-5" />
          </IconButton>
        </div>
      </div>
      <div v-if="loadingError" class="h-full max-h-[70vh] flex flex-col items-center justify-center space-y-4">
        <ExclamationCircleIcon class="w-16 h-16 text-red-500" />
        <p class="text-red-500 text-xl">
          {{ __('NovaFileManager.pintura.loadingError') }}
        </p>
      </div>
      <div v-else class="h-full max-h-[70vh]" ref="editorRef"></div>
      <UploadCropModal
        v-if="uploadIsOpen"
        :file="file"
        :name="UPLOAD_CROP_MODAL_NAME"
        :on-submit="submitCrop"
        :dest-file="destFile"
      />
    </DialogPanel>
  </BaseModal>
</template>
