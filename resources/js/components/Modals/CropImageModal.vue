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
                    @click.prevent.stop="closeModal(name)"
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
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>

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
import { useStore } from 'vuex'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import IconButton from '@/components/Elements/IconButton'
import VueCropper from 'vue-cropperjs'
import 'cropperjs/dist/cropper.css'
import Entity from '@/types/Entity'
import UploadCropModal from '@/components/Modals/UploadCropModal'

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
const buttonRef = ref(null)
const cropper = ref(null)
const blob = ref(null)

const darkMode = computed(() => store.state['nova-file-manager'].darkMode)
const isOpen = computed(() => store.getters['nova-file-manager/allModals'].includes(props.name))
const uploadIsOpen = computed(() =>
    store.getters['nova-file-manager/allModals'].includes('upload-crop')
)

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

const openModal = name => {
    return store.dispatch('nova-file-manager/openModal', name)
}

const closeModal = name => store.dispatch('nova-file-manager/closeModal', name)

const containerStyle = computed(() => ({
    height: '100%',
    minHeight: '60vh',
}))

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
