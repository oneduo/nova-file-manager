<template>
  <BaseModal as="template" class="nova-file-manager" :name="name">
      <DialogPanel
          class="relative bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden shadow-xl transform transition-all w-full max-w-7xl p-4 flex flex-col gap-4 h-[80vh] max-h-[80vh]"
      >
          <div
              class="w-full flex flex-col flex-col-reverse gap-2 md:flex-row justify-between items-start"
          >
              <h2 class="text-lg font-medium text-gray-900 dark:text-gray-400 break-all w-full">
                  {{ __('NovaFileManager.actions.cropImage', { image: file.name }) }}
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

          <div class="h-full max-h-[70vh]">
              <vue-cropper
                  ref="cropper"
                  :containerStyle="containerStyle"
                  :src="file.url"
                  alt="file.name"
                  :viewMode="1"
              >
              </vue-cropper>
          </div>
      </DialogPanel>
  </BaseModal>

  <UploadCropModal
    v-if="uploadIsOpen"
    :file="file"
    name="upload-crop"
    :on-submit="submitCrop"
    :data="cropData"
  />
</template>

<script setup>
import { computed, ref } from 'vue'
import VueCropper from 'vue-cropperjs'
import { DialogPanel } from '@headlessui/vue'
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import IconButton from '@/components/Elements/IconButton'
import Entity from '@/types/Entity'
import BaseModal from '@/components/Modals/BaseModal'
import UploadCropModal from '@/components/Modals/UploadCropModal'
import { useStore } from '@/store'
import 'cropperjs/dist/cropper.css'

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
const cropper = ref(null)
const blob = ref(null)
const uploadIsOpen = computed(() => store.isOpen('upload-crop'))

const containerStyle = computed(() => ({
  height: '100%',
  minHeight: '60vh',
}))

const cropData = computed(() => {
  const data = cropper.value.getData()

  const suffix = `${Math.round(data.width)}_${Math.round(data.height)}_${Math.round(
    data.x
  )}_${Math.round(data.y)}`

  return {
    blob: blob.value,
    name: props.file?.name.replace(props.file?.extension, `${suffix}.${props.file?.extension}`),
  }
})

// ACTIONS
const openModal = name => store.openModal({ name })
const closeModal = name => store.closeModal({ name })

const openUploadCropModal = () => {
  cropper.value.getCroppedCanvas().toBlob(b => {
    blob.value = b

    openModal('upload-crop')
  })
}

const submitCrop = name => {
  const file = new File([blob.value], name, {
    type: props.file.mime,
  })

  closeModal('upload-crop')
  closeModal(props.name)

  props.onConfirm(file)
}
</script>
