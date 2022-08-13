<template>
  <TransitionRoot :show="isOpen" as="template" class="nova-file-manager">
    <Dialog
      as="div"
      class="relative z-[60]"
      style="z-index: 999"
      @close="closeModal"
      @dragover.prevent.stop="dragenter"
      @dragleave.prevent.stop="dragleave"
      @drop.prevent="onDrop"
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
          :class="[
            'fixed inset-0  backdrop-blur-sm transition-opacity',
            active ? 'bg-green-800/20' : 'bg-gray-800/20',
          ]"
        />
      </TransitionChild>

      <div
        :class="darkMode && 'dark'"
        class="fixed z-10 inset-0 overflow-y-auto"
      >
        <div
          class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0"
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
              class="relative bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full"
            >
              <div
                class="max-w-lg flex justify-center px-6 pt-5 pb-6 rounded-md min-h-1/3"
              >
                <div v-if="!isUploading" class="space-y-1 text-center">
                  <CloudUploadIcon
                    :class="[
                      'mx-auto h-12 w-12 text-gray-400',
                      active && 'animate-bounce',
                    ]"
                  />
                  <div class="flex text-sm text-gray-600">
                    <label
                      class="relative cursor-pointer rounded-md font-medium text-blue-500 hover:underline focus-within:outline-"
                      for="file-upload"
                    >
                      <span>{{ __('Upload a file') }}</span>
                      <input
                        id="file-upload"
                        class="sr-only"
                        name="file-upload"
                        type="file"
                        @change="onChange"
                      />
                    </label>
                    <p class="pl-1 text-gray-500">
                      {{ __('or drag and drop') }}
                    </p>
                  </div>
                </div>
                <div v-else class="text-center">
                  <Spinner class="mx-auto h-12 w-12"/>
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { useStore } from 'vuex'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot, } from '@headlessui/vue'
import { CloudUploadIcon } from '@heroicons/vue/outline'
import Spinner from '@/components/Elements/Spinner'

const store = useStore()
const props = defineProps(['name'])
const darkMode = computed(() => store.state['nova-file-manager'].darkMode)
const isUploading = computed(() => store.state['nova-file-manager'].isUploading)
const isOpen = computed(() =>
    store.getters['nova-file-manager/allModals'].includes(props.name)
)

const active = ref(false)
const file = ref(null)

const closeModal = () =>
    store.dispatch('nova-file-manager/closeModal', props.name)
const dragenter = () => (active.value = true)
const dragleave = () => (active.value = false)
const onDrop = e => (file.value = e.dataTransfer.files[0])
const onChange = e => (file.value = e.target.files[0])
const submit = () => {
    if (file.value) {
        store.dispatch('nova-file-manager/upload', file.value)
    }
}

onBeforeUnmount(() => {
    if (isOpen.value) {
        closeModal()
    }
})

watch(file, () => submit())
</script>
