<template>
  <TransitionRoot
    as="template"
    :show="isOpen"
    class="nova-file-manager"
  >
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
        class="fixed z-10 inset-0 overflow-y-auto"
        :class="darkMode && 'dark'"
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
              <div class="max-w-lg flex justify-center px-6 pt-5 pb-6 rounded-md min-h-1/3">
                <div
                  class="space-y-1 text-center"
                  v-if="!isUploading"
                >
                  <CloudUploadIcon
                    :class="['mx-auto h-12 w-12 text-gray-400', active && 'animate-bounce']"
                  />
                  <div class="flex text-sm text-gray-600">
                    <label
                      for="file-upload"
                      class="relative cursor-pointer rounded-md font-medium text-blue-500 hover:underline focus-within:outline-"
                    >
                      <span>{{ __('Upload a file') }}</span>
                      <input
                        id="file-upload"
                        name="file-upload"
                        type="file"
                        class="sr-only"
                        @change="onChange"
                      />
                    </label>
                    <p class="pl-1 text-gray-500">{{ __('or drag and drop') }}</p>
                  </div>
                </div>
                <div
                  class="text-center"
                  v-else
                >
                  <Spinner class="mx-auto h-12 w-12" />
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { CloudUploadIcon } from '@heroicons/vue/outline'
import Spinner from '@/components/Elements/Spinner'
import { mapActions, mapState } from 'vuex'

export default {
  name: 'UploadModal',
  components: {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
    CloudUploadIcon,
    Spinner,
  },
  props: ['name'],
  data() {
    return {
      active: false,
      file: null,
    }
  },
  computed: {
    ...mapState('nova-file-manager', ['darkMode', 'isUploading']),
    isOpen() {
      return this.$store.getters['nova-file-manager/allModals'].includes(this.name)
    },
  },

  methods: {
    ...mapActions('nova-file-manager', ['upload']),
    closeModal() {
      this.$store.dispatch('nova-file-manager/closeModal', this.name)
    },
    setFile(input) {
      this.file = input
    },
    dragenter() {
      this.active = true
    },
    dragleave() {
      this.active = false
    },
    onDrop(e) {
      this.file = e.dataTransfer.files[0]
    },
    onChange(e) {
      this.file = e.target.files[0]
    },
    submit() {
      if (!!this.file) {
        this.upload(this.file)
      }
    },
  },
  watch: {
    file() {
      this.submit()
    },
  },
  beforeDestroy() {
    if (this.isOpen) this.closeModal()
  },
}
</script>
