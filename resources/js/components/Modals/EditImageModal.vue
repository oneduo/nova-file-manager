<template>
  <BaseModal as="template" class="nova-file-manager" :name="name" v-slot="{ close }">
    <DialogPanel
      class="relative bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden shadow-xl transform transition-all w-full max-w-7xl p-4 flex flex-col gap-4 h-[80vh] max-h-[80vh]"
    >
      <div
        class="w-full flex flex-col flex-col-reverse gap-2 md:flex-row justify-between items-start"
      >
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-400 break-all w-full">
          {{ __('NovaFileManager.actions.editImage', { image: file.name }) }}
        </h2>

        <div class="flex flex-row gap-2 justify-end flex-shrink-0">
          <IconButton
            ref="buttonRef"
            :title="__('NovaFileManager.actions.close')"
            @click.prevent.stop="close"
          >
            <XMarkIcon class="w-5 h-5" />
          </IconButton>

          <IconButton variant="success" @click="openUploadCropModal">
            <CheckIcon class="h-5 w-5" />
          </IconButton>
        </div>
      </div>
      <div
        v-if="loadingError"
        class="h-full max-h-[70vh] flex flex-col items-center justify-center space-y-4"
      >
        <ExclamationCircleIcon class="w-16 h-16 text-red-500" />
        <p class="text-red-500 text-xl">
          {{ __('NovaFileManager.pintura.loadingError') }}
        </p>
      </div>
      <div v-else class="h-full max-h-[70vh]" ref="editorRef"></div>
      <UploadCropModal
        v-if="uploadIsOpen"
        :file="file"
        name="upload-crop"
        :on-submit="submitCrop"
        :dest-file="destFile"
      />
    </DialogPanel>
  </BaseModal>
</template>

<script setup>
import { computed, ref, watchEffect } from 'vue'
import { DialogPanel } from '@headlessui/vue'
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { ExclamationCircleIcon } from '@heroicons/vue/24/solid'
import IconButton from '@/components/Elements/IconButton'
import Entity from '@/types/Entity'
import BaseModal from '@/components/Modals/BaseModal'
import UploadCropModal from '@/components/Modals/UploadCropModal'
import { useStore } from '@/store'
import 'cropperjs/dist/cropper.css'
import { usePintura } from '@/hooks'

const props = defineProps({
  file: {
    type: Entity,
    required: true,
  },
  name: {
    type: String,
    required: true,
  },
  onConfirm: {
    type: Function,
    required: true,
  },
})

const store = useStore()

//STATE
const buttonRef = ref(null)
const loadingError = ref(null)
const destFile = ref(null)
const uploadIsOpen = computed(() => store.isOpen('upload-crop'))
const editorRef = ref(null)
const editor = ref(null)
const { pinturaOptions } = usePintura()

watchEffect(() => {
  if (editorRef.value && !editor.value) {
    try {
      const { appendEditor, editorOptions } = window.Nova.config.NovaFileManagerEditor

      editor.value = appendEditor(editorRef.value, {
        ...editorOptions,
        ...pinturaOptions.value,
        src: props.file.url,
        enableButtonExport: false,
      })

      if (editor.value) {
        editor.value.on('loaderror', ({ error }) => Nova.error(error.message))
      }
    } catch (e) {
      loadingError.value = true
      console.error(e)
    }
  }
})

// ACTIONS
const openModal = name => store.openModal({ name })
const closeModal = name => store.closeModal({ name })

const openUploadCropModal = () => {
  editor.value.processImage().then(({ dest }) => {
    destFile.value = dest

    openModal('upload-crop')
  })
}

const submitCrop = name => {
  const file = new File([destFile.value], name, {
    type: props.file.mime,
  })

  closeModal('upload-crop')
  closeModal(props.name)

  props.onConfirm(file)
}
</script>
